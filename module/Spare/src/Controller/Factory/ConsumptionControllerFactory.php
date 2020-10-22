<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Reference\Service\VehicleService;
use Spare\Controller\ConsumptionController;
use Reference\Service\WarehouseService;
use Reference\Service\EmployeeService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConsumptionControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ConsumptionController
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $accessValidateService = $container->get('accessValidateService');
        $services = [
            'spareConsumptionService' => $container->get('spareConsumptionService'),
            'spareService' => $container->get('spareService'),
            'spareTotalService' => $container->get('spareTotalService'),
            'employeeService' => $container->get(EmployeeService::class),
            'warehouseService' => $container->get(WarehouseService::class),
            'vehicleService' => $container->get(VehicleService::class),
            'inventoryService' => $container->get('spareInventoryService')
        ];

        return new ConsumptionController($entityManager, $services, $accessValidateService);
    }
}
