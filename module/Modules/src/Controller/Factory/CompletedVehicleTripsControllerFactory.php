<?php

namespace Modules\Controller\Factory;

use \Core\Controller\Factory\CrudControllerFactory;
use \Interop\Container\ContainerInterface;
use Modules\Service\CompletedVehicleTripsService;
use Reference\Service\DepartmentService;
use Reports\Service\RemoteSkladService;

class CompletedVehicleTripsControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get(CompletedVehicleTripsService::class);
        $services = [
            RemoteSkladService::class => $container->get(RemoteSkladService::class),
            DepartmentService::class => $container->get(DepartmentService::class)
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
