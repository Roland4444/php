<?php

namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Reference\Service\MetalGroupService;
use Reference\Service\MetalService;
use Storage\Service\ContainerItemService;
use Storage\Service\PurchaseService;
use Storage\Service\TransferService;

/**
 * Class BalanceControllerFactory
 * @package Storage\Controller\Factory
 */
class BalanceControllerFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $services = [
            MetalService::class => $container->get(MetalService::class),
            DepartmentService::class => $container->get(DepartmentService::class),
            MetalGroupService::class => $container->get(MetalGroupService::class),
            PurchaseService::class => $container->get(PurchaseService::class),
            ContainerItemService::class => $container->get(ContainerItemService::class),
            TransferService::class => $container->get(TransferService::class),
        ];
        return new $requestedName($entityManager, $services);
    }
}
