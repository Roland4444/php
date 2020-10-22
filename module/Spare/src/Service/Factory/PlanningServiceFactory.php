<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\Planning;
use Spare\Service\PlanningService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PlanningServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PlanningService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Planning::class);
        $planningStatusService = $container->get('sparePlanningStatusService');
        $planningItemService = $container->get('sparePlanningItemsService');
        return new PlanningService($repository, $planningStatusService, $planningItemService);
    }
}
