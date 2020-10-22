<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Spare\Controller\OrderController;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return OrderController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $accessValidateService = $container->get('accessValidateService');
        $services = [
            'spareOrderService' => $container->get('spareOrderService'),
            'spareService' => $container->get('spareService'),
            'planningItemsService' => $container->get('sparePlanningItemsService'),
            'planningService' => $container->get('sparePlanningService'),
            'sellerService' => $container->get('spareSellerService'),
            'orderItemsService' => $container->get('spareOrderItemsService'),
            'dateService' => $container->get('dateService'),
        ];

        return new OrderController($entityManager, $services, $accessValidateService);
    }
}
