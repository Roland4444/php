<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Finance\Service\BankService;
use Reference\Service\CategoryService;
use Reference\Service\SettingsService;

class DiamondCommissionServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settingsService = $container->get(SettingsService::class);
        $expenseService = $container->get('financeOtherExpenseService');
        $categoryService = $container->get(CategoryService::class);
        $bankService = $container->get(BankService::class);
        return new DiamondCommissionService($settingsService, $expenseService, $categoryService, $bankService);
    }
}
