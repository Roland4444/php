<?php

namespace Storage\Facade;

use Interop\Container\ContainerInterface;
use Storage\Service\ContainerService;
use Storage\Service\ShipmentService;

class StorageFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $shipmentService = $container->get(ShipmentService::class);
        $containerService = $container->get(ContainerService::class);
        $officeCash = $container->get('officeCash');
        return new Storage($shipmentService, $containerService, $officeCash);
    }
}
