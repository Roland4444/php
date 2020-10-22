<?php

namespace Api\Controller;

use Interop\Container\ContainerInterface;
use Reference\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $authService = $container->get('authenticationService');
        $logService = $container->get('authlogService');
        $userService = $container->get(UserService::class);
        return new AuthController($entityManager, $authService, $logService, $userService);
    }
}
