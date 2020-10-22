<?php

namespace ShipmentDocs\Controller\Factory;

use Interop\Container\ContainerInterface;
use ShipmentDocs\Service\ApiService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(ApiService::class);
        return new $requestedName($service);
    }
}
