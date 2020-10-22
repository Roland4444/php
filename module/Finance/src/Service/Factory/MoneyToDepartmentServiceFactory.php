<?php

namespace Finance\Service\Factory;

use Finance\Service\BankService;
use Finance\Service\MoneyToDepartmentService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MoneyToDepartmentServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $bankAccountService = $container->get(BankService::class);
        return new MoneyToDepartmentService($entityManager, $bankAccountService);
    }
}
