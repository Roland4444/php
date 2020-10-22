<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\WarehouseService;
use Spare\Entity\Inventory;
use Spare\Service\InventoryService;
use Zend\ServiceManager\Factory\FactoryInterface;

class InventoryServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return InventoryService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $inventoryRepository = $entityManager->getRepository(Inventory::class);
        $spareService = $container->get('spareService');
        $warehouseService = $container->get(WarehouseService::class);
        return new InventoryService($inventoryRepository, $spareService, $warehouseService);
    }
}
