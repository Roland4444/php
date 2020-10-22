<?php

namespace Storage\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Interop\Container\ContainerInterface;
use Finance\Service\MoneyToDepartmentService;
use Finance\Service\BankService;
use Reference\Service\DepartmentService;
use Reference\Service\MetalService;

/**
 * Class CashInControllerFactory
 * @package Storage\Controller\Factory
 */
class CashInControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get(MoneyToDepartmentService::class);
        $services = [
            MetalService::class => $container->get(MetalService::class),
            DepartmentService::class => $container->get(DepartmentService::class),
            BankService::class => $container->get(BankService::class),
            'dateService' => $container->get('dateService'),
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
