<?php

namespace Reference\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Form\CategoryForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class CategoryFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new CategoryForm($entityManager);
    }
}
