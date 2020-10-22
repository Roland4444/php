<?php

namespace Storage\Form\Factory;

use Interop\Container\ContainerInterface;
use Storage\Form\ContainerForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerFormFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new ContainerForm($entityManager);
    }
}
