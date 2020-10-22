<?php

namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\CustomerDebt;
use Zend\ServiceManager\Factory\FactoryInterface;

class CustomerDebtServiceFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(CustomerDebt::class);
        return new CustomerDebtService($repository);
    }
}
