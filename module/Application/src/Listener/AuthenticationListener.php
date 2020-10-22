<?php

namespace Application\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Session\Container;

class AuthenticationListener extends AbstractListenerAggregate
{
    private $authorizationMethod = 'browser';

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'initAcl']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkToken']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAccess']);
    }

    public function checkToken($event)
    {
        $request = $event->getApplication()->getRequest();
        if ($request instanceof \Zend\Console\Request) {
            return;
        }
        $token = $request->getHeader('Authorization');
        if (! empty($token)) {
            try {
                $this->authorizationMethod = 'api';
                $container = $event->getApplication()->getServiceManager();
                $authService = $container->get('authService');
                $authService->authByToken($token->getFieldValue());
            } catch (\Exception $e) {
                $response = $event->getResponse();
                $response->setStatusCode(401);
                $response->setContent($e->getMessage());
                return $response;
            }
        }
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function initAcl($event)
    {
        $acl = new Acl();
        $config = $event->getApplication()->getServiceManager()->get('Config');

        $params = $config['aclmodule'];

        if (array_key_exists('allow', $params)) {
            $allow = $params['allow'];
            foreach ($allow as $rule) {
                $roles = is_array($rule[0]) ? $rule[0] : [$rule[0]];
                $resources = $rule[1] ?? null;
                $privileges = $rule[2] ?? null;
                foreach ($roles as $role) {
                    if (! $acl->hasRole($role)) {
                        $acl->addRole($role);
                    }
                    if (is_array($resources)) {
                        foreach ($resources as $resource) {
                            if (! $acl->hasResource($resource)) {
                                $acl->addResource($resource);
                            }
                        }
                    } elseif (is_string($resources)) {
                        if (! $acl->hasResource($resources)) {
                            $acl->addResource($resources);
                        }
                    }
                    $acl->allow($role, $resources, $privileges);
                }
            }
        }

        $controllers = $config['controllers'];
        if (array_key_exists('invokables', $controllers)) {
            foreach ($controllers['invokables'] as $key => $controller) {
                if (! $acl->hasResource($key)) {
                    $acl->addResource($key);
                }
            }
        }

        $serviceManager = $event->getApplication()->getServiceManager();
        $serviceManager->setService('acl', $acl);
    }

    public function checkAccess($e)
    {
        if ($e->getApplication()->getResponse() instanceof
            \Zend\Console\Response) {
            return;
        }
        $route = $e->getRouteMatch();
        $serviceManager = $e->getApplication()->getServiceManager();
        $authService = $serviceManager->get('authenticationService');
        $identity = $authService->getIdentity();
        $roles = $identity ? $identity->getRoleNames() : ['guest'];

        $acl = $serviceManager->get('acl');

        if ($acl->hasResource($route->getParam('controller'))) {
            $isPermitted = false;
            foreach ($roles as $role) {
                if ($acl->isAllowed($role, $route->getParam('controller'), $route->getParam('action'))) {
                    $isPermitted = true;
                    break;
                }
            }
            if (! $isPermitted) {
                $this->logout($e);
            }

            if ($identity) {
                $session = new Container();

                if ($session->check != md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) {
                    if ($this->authorizationMethod == 'browser') {
                        $authService->clearIdentity();
                        $this->logout($e);
                    }
                } elseif ($identity->getIsBlocked()) {
                    $authService->clearIdentity();
                    $this->logout($e, 'blocked');
                } elseif (! $identity->getLoginFromInternet()) {
                    $config = $e->getApplication()->getServiceManager()
                        ->get('Config');

                    if (! in_array($this->getNetwork(), $config['networks'])) {
                        $authService->clearIdentity();
                        $this->logout($e, 'badnetwork');
                    }
                } elseif ($identity->getChangePassword()
                    && ! in_array($route->getParam('action'), ['changepass', 'logout'])) {
                    $this->logout($e);
                }
            }
        } else {
            throw new \Exception('Resource ' . $route->getParam('controller') .
                ' not defined');
        }
    }

    private function logout($e, $type = null)
    {
        $url = $e->getRouter()->assemble([], ['name' => 'login']);

        $response = $e->getResponse();
        $response->setHeaders($response->getHeaders()
            ->addHeaderLine('Location', $url . '?' . $type));
        $response->setStatusCode(302);
        $response->sendHeaders();
        exit;
    }

    private function getNetwork()
    {
        preg_match(
            '/^(\d{1,3}\.\d{1,3}\.\d{1,3})\.\d{1,3}\z/',
            $_SERVER['REMOTE_ADDR'],
            $matches
        );
        return end($matches);
    }
}
