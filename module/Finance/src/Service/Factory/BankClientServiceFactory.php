<?php

namespace Finance\Service\Factory;

use Finance\Service\BankClientService;
use Finance\Service\BankService;
use Finance\Service\OtherReceiptService;
use Interop\Container\ContainerInterface;
use Reference\Service\CategoryService;
use Reference\Service\CustomerService;
use Reference\Service\TraderService;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankClientServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $services['bankAccountService'] = $container->get(BankService::class);
        $services['traderService'] = $container->get(TraderService::class);
        $services['templateService'] = $container->get('financePaymentTemplateService');
        $services['traderReceiptService'] = $container->get('traderReceiptsService');
        $services['categoryService'] = $container->get(CategoryService::class);
        $services['otherExpenseService'] = $container->get('financeOtherExpenseService');
        $services['cashTransferService'] = $container->get('cashTransferService');
        $services['otherReceiptService'] = $container->get(OtherReceiptService::class);
        $services['metalExpenseService'] = $container->get('financeMetalExpense');
        $services['customerService'] = $container->get(CustomerService::class);
        $services['factoring'] = $container->get('factoring');
        $services['dateService'] = $container->get('dateService');
        return new BankClientService($services);
    }
}
