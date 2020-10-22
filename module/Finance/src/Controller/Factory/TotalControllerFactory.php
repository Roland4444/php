<?php

namespace Finance\Controller\Factory;

use Finance\Controller\TotalController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TotalControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $financeTotalService = $container->get('financeTotalService');
        return new TotalController($financeTotalService);
    }
}
