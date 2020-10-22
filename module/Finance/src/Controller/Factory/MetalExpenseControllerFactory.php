<?php

namespace Finance\Controller\Factory;

use Finance\Controller\MetalExpenseController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Zend\ServiceManager\Factory\FactoryInterface;

class MetalExpenseControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $metalExpenseService = $container->get('financeMetalExpense');
        $customerService = $container->get(CustomerService::class);
        $bankService = $container->get(BankService::class);
        return new MetalExpenseController($metalExpenseService, $customerService, $bankService);
    }
}
