<?php

namespace Finance\Service\Factory;

use Finance\Entity\MetalExpense;
use Finance\Service\MetalExpenseService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MetalExpenseServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(MetalExpense::class);
        return new MetalExpenseService($repository);
    }
}
