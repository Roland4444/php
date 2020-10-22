<?php

namespace Api\Service;

use Interop\Container\ContainerInterface;
use Reference\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthServiceFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userService = $container->get(UserService::class);
        $tokenAuthAdapter = $container->get('tokenAuthAdapter');
        $authService = $container->get('authenticationService');
        return new AuthService($userService, $tokenAuthAdapter, $authService);
    }
}
