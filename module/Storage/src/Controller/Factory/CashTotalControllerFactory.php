<?php

namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Controller\CashTotalController;
use Storage\Service\CashService;

/**
 * Class CashTotalControllerFactory
 * @package Storage\Controller\Factory
 */
class CashTotalControllerFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $cashService = $container->get(CashService::class);
        $services = [
            DepartmentService::class => $container->get(DepartmentService::class),
            CustomerService::class => $container->get(CustomerService::class),
        ];

        return new CashTotalController($entityManager, $services, $cashService);
    }
}
