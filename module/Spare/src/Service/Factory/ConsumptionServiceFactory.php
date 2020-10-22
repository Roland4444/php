<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\EmployeeService;
use Reference\Service\WarehouseService;
use Spare\Entity\Consumption;
use Spare\Service\ConsumptionService;
use Spare\Service\InventoryService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConsumptionServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Consumption::class);
        $consumptionItemService = $container->get('spareConsumptionItemService');
        $employeeService = $container->get(EmployeeService::class);
        $warehouseService = $container->get(WarehouseService::class);
        $inventoryService = $container->get('spareInventoryService');

        return new ConsumptionService($repository, $consumptionItemService, $employeeService, $warehouseService, $inventoryService);
    }
}
