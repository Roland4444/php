<?php

namespace Storage\Service\Factory;

use Finance\Service\MoneyToDepartmentService;
use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Service\WeighingService;
use Storage\Entity\MetalExpense;
use Storage\Service\MetalExpenseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class MetalExpenseServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(MetalExpense::class);
        $moneyToDepartmentService = $container->get(MoneyToDepartmentService::class);
        $customerService = $container->get(CustomerService::class);
        $departmentService = $container->get(DepartmentService::class);
        $weighingService = $container->get(WeighingService::class);
        $authService = $container->get('authenticationService');

        return new MetalExpenseService($repository, $moneyToDepartmentService, $customerService, $departmentService, $weighingService, $authService);
    }
}
