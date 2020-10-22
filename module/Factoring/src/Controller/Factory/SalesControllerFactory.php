<?php

namespace Factoring\Controller\Factory;

use Factoring\Controller\SalesController;
use \Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SalesControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return SalesController
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get('factoringSalesService');
        return new SalesController($service);
    }
}
