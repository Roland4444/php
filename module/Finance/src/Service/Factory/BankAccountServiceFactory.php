<?php

namespace Finance\Service\Factory;

use Finance\Entity\BankAccount;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankAccountServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(BankAccount::class);
        return new BankService($repository);
    }
}
