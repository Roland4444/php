<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Service\PlanningItemsService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PlanningItemsServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PlanningItemsService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new PlanningItemsService($entityManager);
    }
}
