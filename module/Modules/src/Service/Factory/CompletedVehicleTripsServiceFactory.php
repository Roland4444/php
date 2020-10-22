<?php

namespace Modules\Service\Factory;

use Interop\Container\ContainerInterface;
use Modules\Service\CompletedVehicleTripsService;
use Modules\Service\TransportIncasService;
use Reports\Service\RemoteSkladService;

class CompletedVehicleTripsServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CompletedVehicleTripsService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $remoteSkladService = $container->get(RemoteSkladService::class);
        $incomeService = $container->get(TransportIncasService::class);
        return new $requestedName($entityManager, $remoteSkladService, $incomeService);
    }
}
