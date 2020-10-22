<?php

namespace Application\Job;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DiamondCommissionJobFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $logger = $container->get('MyLogger');
        $entityManager = $container->get('entityManager');
        $diamondCommissionService = $container->get('diamondCommission');
        return new DiamondCommissionJob($entityManager, $logger, $diamondCommissionService);
    }
}
