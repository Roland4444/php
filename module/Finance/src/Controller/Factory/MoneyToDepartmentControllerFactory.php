<?php

namespace Finance\Controller\Factory;

use Finance\Controller\MoneyToDepartmentController;
use Finance\Service\BankService;
use Finance\Service\MoneyToDepartmentService;
use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class MoneyToDepartmentControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $departmentService = $container->get(DepartmentService::class);
        $authService = $container->get('authenticationService');
        $moneyToDepartmentService = $container->get(MoneyToDepartmentService::class);
        $bankService = $container->get(BankService::class);
        return new MoneyToDepartmentController($departmentService, $authService, $moneyToDepartmentService, $bankService);
    }
}
