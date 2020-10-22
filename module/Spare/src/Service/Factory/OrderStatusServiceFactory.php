<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\OrderStatus;
use Spare\Service\OrderStatusService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderStatusServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OrderStatusService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(OrderStatus::class);
        return new OrderStatusService($repository);
    }
}
