<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\Order;
use Spare\Service\OrderService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OrderService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Order::class);
        $orderStatusService = $container->get('spareOrderStatusService');
        $orderPaymentStatusService = $container->get('spareOrderPaymentStatusService');
        return new OrderService($repository, $orderStatusService, $orderPaymentStatusService);
    }
}
