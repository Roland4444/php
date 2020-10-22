<?php

namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Storage\Entity\Purchase;
use Storage\Service\PurchaseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Purchase::class);
        return new PurchaseService($repository);
    }
}
