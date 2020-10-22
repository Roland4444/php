<?php

namespace Storage\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Service\MetalExpenseService;
use Storage\Service\PurchaseDealService;

/**
 * Class MetalExpenseControllerFactory
 * @package Storage\Controller\Factory
 */
class MetalExpenseControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get(MetalExpenseService::class);
        $services = [
            DepartmentService::class => $container->get(DepartmentService::class),
            CustomerService::class => $container->get(CustomerService::class),
            MetalExpenseService::class => $container->get(MetalExpenseService::class),
            PurchaseDealService::class => $container->get(PurchaseDealService::class),
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
