<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\PlanningStatus;
use Spare\Service\PlanningStatusService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PlanningStatusServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PlanningStatusService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(PlanningStatus::class);
        return new PlanningStatusService($repository);
    }
}
