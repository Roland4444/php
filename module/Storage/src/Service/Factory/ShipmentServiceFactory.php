<?php

namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\ShipmentTariffService;
use Reference\Service\TraderService;
use Storage\Entity\Shipment;
use Storage\Service\ContainerService;
use Storage\Service\ShipmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ShipmentServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Shipment::class);
        $containerService = $container->get(ContainerService::class);
        $traderService = $container->get(TraderService::class);
        $tariffService = $container->get(ShipmentTariffService::class);
        return new ShipmentService($repository, $containerService, $traderService, $tariffService);
    }
}
