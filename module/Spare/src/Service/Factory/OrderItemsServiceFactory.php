<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\OrderItems;
use Spare\Service\OrderItemsService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderItemsServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OrderItemsService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(OrderItems::class);
        return new OrderItemsService($repository);
    }
}
