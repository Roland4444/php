<?php

namespace OfficeCash\Controller\Factory;

use Finance\Service\MoneyToDepartmentService;
use Interop\Container\ContainerInterface;
use OfficeCash\Service\TransportIncomeService;
use Reference\Service\DepartmentService;
use OfficeCash\Controller\OfficeCashTotalController;
use OfficeCash\Service\OtherExpenseService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OfficeCashTotalControllerFactory
 * @package Storage\Controller\Factory
 */
class OfficeCashTotalControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $departmentService = $container->get(DepartmentService::class);
        $transportIncomeService = $container->get(TransportIncomeService::class);
        $moneyToDepartmentService = $container->get(MoneyToDepartmentService::class);
        $storageOtherExpenseService = $container->get(OtherExpenseService::class);
        return new OfficeCashTotalController($departmentService, $transportIncomeService, $moneyToDepartmentService, $storageOtherExpenseService);
    }
}
