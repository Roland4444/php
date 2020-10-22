<?php

namespace Storage\Service\Factory;

use Finance\Service\MoneyToDepartmentService;
use Interop\Container\ContainerInterface;
use Storage\Dao\CashTotalDao;
use Storage\Service\CashService;
use Storage\Service\CashTransferService;
use Storage\Service\MetalExpenseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class CashServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moneyToDepartmentService = $container->get(MoneyToDepartmentService::class);
        $storageMetalExpenseService = $container->get(MetalExpenseService::class);
        $cashTransferService = $container->get(CashTransferService::class);
        $dao = $container->get(CashTotalDao::class);
        return new CashService($moneyToDepartmentService, $storageMetalExpenseService, $cashTransferService, $dao);
    }
}
