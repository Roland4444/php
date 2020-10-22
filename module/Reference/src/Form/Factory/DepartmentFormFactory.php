<?php

namespace Reference\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Form\DepartmentForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class DepartmentFormFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new DepartmentForm($entityManager);
    }
}
