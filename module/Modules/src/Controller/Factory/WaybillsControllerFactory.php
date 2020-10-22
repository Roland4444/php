<?php

namespace Modules\Controller\Factory;

use \Core\Controller\Factory\CrudControllerFactory;
use \Interop\Container\ContainerInterface;
use Modules\Service\CompletedVehicleTripsService;
use Modules\Service\WaybillSettingsService;
use Modules\Service\WaybillsService;
use Reports\Service\RemoteSkladService;

class WaybillsControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get(WaybillsService::class);
        $services = [
            CompletedVehicleTripsService::class => $container->get(CompletedVehicleTripsService::class),
            WaybillsService::class => $container->get(WaybillsService::class),
            WaybillSettingsService::class => $container->get(WaybillSettingsService::class),
            RemoteSkladService::class => $container->get(RemoteSkladService::class),
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
