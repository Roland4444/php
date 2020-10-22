<?php

namespace Core\Service\Factory;

use Core\Service\AccessValidateService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccessValidateServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get('authenticationService');
        $dateService = $container->get('dateService');
        return new AccessValidateService($authService, $dateService);
    }
}
