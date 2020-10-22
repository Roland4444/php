<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Reference\Service\EmployeeService;
use Reference\Service\WarehouseService;
use Spare\Controller\TransferController;
use Spare\Service\SpareService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransferControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TransferController
     * @throws
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $accessValidateService = $container->get('accessValidateService');
        $services = [
            'spareTransferService' => $container->get('spareTransferService'),
            SpareService::class => $container->get('spareService'),
            'spareTotalService' => $container->get('spareTotalService'),
            'employeeService' => $container->get(EmployeeService::class),
            'warehouseService' => $container->get(WarehouseService::class),
            'dateService' => $container->get('dateService'),
            'inventoryService' => $container->get('spareInventoryService')
        ];

        return new TransferController($entityManager, $services, $accessValidateService);
    }
}
