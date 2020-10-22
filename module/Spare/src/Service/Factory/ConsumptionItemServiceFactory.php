<?php


namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\VehicleService;
use Spare\Entity\ConsumptionItem;
use Spare\Service\ConsumptionItemService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConsumptionItemServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(ConsumptionItem::class);
        $vehicleService = $container->get(VehicleService::class);
        $spareService = $container->get('spareService');

        return new ConsumptionItemService($repository, $vehicleService, $spareService);
    }
}
