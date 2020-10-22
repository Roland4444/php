<?php

namespace Reports\Service\Factory;

use Interop\Container\ContainerInterface;
use Reports\Service\ExpensesReportService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ExpensesReportServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $financeExpenseService = $container->get('financeOtherExpenseService');
        $reports = $container->get('reports');
        return new ExpensesReportService($financeExpenseService, $entityManager, $reports);
    }
}
