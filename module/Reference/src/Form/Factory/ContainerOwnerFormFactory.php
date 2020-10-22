<?php

namespace Reference\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Form\ContainerOwnerForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerOwnerFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new ContainerOwnerForm($entityManager);
    }
}
