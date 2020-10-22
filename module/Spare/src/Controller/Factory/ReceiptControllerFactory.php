<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Spare\Controller\ReceiptController;
use Reference\Service\WarehouseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ReceiptControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ReceiptController
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $accessValidateService = $container->get('accessValidateService');
        $services = [
            'spareReceiptService' => $container->get('spareReceiptService'),
            'planningService' => $container->get('sparePlanningService'),
            'orderService' => $container->get('spareOrderService'),
            'sellerService' => $container->get('spareSellerService'),
            'orderItemsService' => $container->get('spareOrderItemsService'),
            'warehouseService' => $container->get(WarehouseService::class),
            'inventoryService' => $container->get('spareInventoryService')
        ];

        return new ReceiptController($entityManager, $services, $accessValidateService);
    }
}
