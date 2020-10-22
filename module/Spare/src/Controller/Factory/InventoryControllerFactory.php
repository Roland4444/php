<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Reference\Service\WarehouseService;
use Spare\Controller\InventoryController;
use Zend\ServiceManager\Factory\FactoryInterface;

class InventoryControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return InventoryController
     * @throws
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $services = [
            'totalService' => $container->get('spareTotalService'),
            'inventoryService' => $container->get('spareInventoryService'),
            'spareService' => $container->get('spareService'),
            'warehouseService' => $container->get(WarehouseService::class),
            'dateService' => $container->get('dateService'),
        ];
        $accessValidateService = $container->get('accessValidateService');
        return new InventoryController($entityManager, $services, $accessValidateService);
    }
}
