<?php

namespace Modules\Controller\Factory;

use Interop\Container\ContainerInterface;
use Modules\Controller\IncomeController;
use Modules\Service\CompletedVehicleTripsService;
use Zend\ServiceManager\Factory\FactoryInterface;

class IncomeControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(CompletedVehicleTripsService::class);
        return new IncomeController($service);
    }
}
