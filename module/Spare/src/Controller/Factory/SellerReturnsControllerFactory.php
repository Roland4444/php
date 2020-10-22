<?php

namespace Spare\Controller\Factory;

use Interop\Container\ContainerInterface;
use Spare\Controller\SellerReturnsController;
use Zend\ServiceManager\Factory\FactoryInterface;

class SellerReturnsControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sellerReturnsService = $container->get('sellerReturnsService');
        $sellerService = $container->get('spareSellerService');

        return new SellerReturnsController($sellerReturnsService, $sellerService);
    }
}
