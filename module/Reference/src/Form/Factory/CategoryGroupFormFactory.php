<?php

namespace Reference\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Form\CategoryGroupForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class CategoryGroupFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new CategoryGroupForm($entityManager);
    }
}
