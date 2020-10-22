<?php

namespace Modules\Controller\Factory;

use \Core\Controller\Factory\CrudControllerFactory;
use \Interop\Container\ContainerInterface;
use Modules\Service\CompletedVehicleTripsService;
use Reference\Service\DepartmentService;
use Reference\Service\VehicleService;
use Reports\Service\RemoteSkladService;
use Modules\Service\ScheduledVehicleTripsService;

class ScheduledVehicleTripsControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get(ScheduledVehicleTripsService::class);
        $services = [
            RemoteSkladService::class => $container->get(RemoteSkladService::class),
            VehicleService::class => $container->get(VehicleService::class),
            CompletedVehicleTripsService::class => $container->get(CompletedVehicleTripsService::class),
            DepartmentService::class => $container->get(DepartmentService::class)
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
