<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Reference\Service\EmployeeService;
use Reference\Service\VehicleService;
use Spare\Controller\PlanningController;
use Zend\ServiceManager\Factory\FactoryInterface;

class PlanningControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return PlanningController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $accessValidateService = $container->get('accessValidateService');
        $services = [
            'sparePlanningService' => $container->get('sparePlanningService'),
            'sparePlanningStatusService' => $container->get('sparePlanningStatusService'),
            'spareService' => $container->get('spareService'),
            'orderItemsService' => $container->get('spareOrderItemsService'),
            'planningItemService' => $container->get('sparePlanningItemsService'),
            'employeeService' => $container->get(EmployeeService::class),
            'vehicleService' => $container->get(VehicleService::class),
        ];
        return new PlanningController($entityManager, $services, $accessValidateService);
    }
}
