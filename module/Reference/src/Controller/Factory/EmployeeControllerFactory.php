<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\EmployeeController;
use Reference\Form\EmployeeForm;
use Reference\Service\EmployeeService;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(EmployeeService::class);
        $form = $container->get(EmployeeForm::class);

        return new EmployeeController($service, $form);
    }
}
