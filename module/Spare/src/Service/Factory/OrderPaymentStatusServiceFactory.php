<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\OrderPaymentStatus;
use Spare\Service\OrderPaymentStatusService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderPaymentStatusServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OrderPaymentStatusService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(OrderPaymentStatus::class);
        return new OrderPaymentStatusService($repository);
    }
}
