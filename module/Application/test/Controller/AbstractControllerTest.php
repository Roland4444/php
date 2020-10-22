<?php

namespace ApplicationTest\Controller;

use Application\View\Helper\ViewHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Reference\Entity\Role;
use Reference\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

abstract class AbstractControllerTest extends AbstractHttpControllerTestCase
{
    protected $user = 'admin';
    protected $roles = ['admin'];

    public function setUp(): void
    {
        ini_set("memory_limit", "256M");
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();
        $this->authMock();
        $this->helperMock();
    }

    protected function helperMock()
    {
        $mockMenu = $this->getMockBuilder(ViewHelper::class)->disableOriginalConstructor()->getMock();
        $mockMenu->expects($this->any())->method('showMenu')->willReturn(true);

        $this->getApplication()->getServiceManager()->get('ViewHelperManager')->setAllowOverride(true);
        $this->getApplication()->getServiceManager()->get('ViewHelperManager')->setService('viewHelper', $mockMenu);
    }

    protected function authMock()
    {
        $mockAuth = $this->getMockBuilder(AuthenticationService::class)->disableOriginalConstructor()->getMock();
        $mockAuth->expects($this->any())->method('hasIdentity')->willReturn(true);

        $user = $this->getMockUser();
        $mockAuth->expects($this->any())->method('getIdentity')->willReturn($user);

        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('authenticationService', $mockAuth);
        $this->getApplicationServiceLocator()->setAllowOverride(false);

        $session = new Container();
        $session->check = md5('127.0.0.1ie');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'ie';
    }

    protected function getMockUser(): User
    {
        $user = new User();
        $user->setLogin($this->user);
        $user->setName($this->user);
        foreach ($this->roles as $roleName) {
            $role = new Role();
            $role->setName($roleName);
            $c = new ArrayCollection([$role]);
            $user->addRoles($c);
        }
        return $user;
    }

    protected function overrideService(string $name, $service): void
    {
        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService($name, $service);
        $this->getApplicationServiceLocator()->setAllowOverride(false);
    }
}
