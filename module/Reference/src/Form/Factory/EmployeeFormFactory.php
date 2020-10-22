<?php

namespace Reference\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Form\EmployeeForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new EmployeeForm($entityManager);
    }
}
