<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\SellerReturn;
use Spare\Service\SellerReturnsService;
use Zend\ServiceManager\Factory\FactoryInterface;

class SellerReturnsServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $sellerService = $container->get('spareSellerService');
        $repository = $entityManager->getRepository(SellerReturn::class);

        return new SellerReturnsService($repository, $sellerService);
    }
}
