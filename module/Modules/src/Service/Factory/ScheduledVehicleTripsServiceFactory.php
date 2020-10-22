<?php

namespace Modules\Service\Factory;

use Interop\Container\ContainerInterface;
use Modules\Service\ScheduledVehicleTripsService;

class ScheduledVehicleTripsServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ScheduledVehicleTripsService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new $requestedName($entityManager);
    }
}
