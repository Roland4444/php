<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\UserController;
use Reference\Form\UserForm;
use Reference\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class UserControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(UserService::class);
        $form = $container->get(UserForm::class);

        $logService = $container->get('authlogService');
        $authService = $container->get('authenticationService');
        $sessionManager = $container->get(SessionManager::class);
        return new UserController($service, $form, $logService, $authService, $sessionManager);
    }
}
