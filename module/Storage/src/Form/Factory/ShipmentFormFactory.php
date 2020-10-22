<?php

namespace Storage\Form\Factory;

use Interop\Container\ContainerInterface;
use Storage\Form\ShipmentForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class ShipmentFormFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new ShipmentForm($entityManager);
    }
}
