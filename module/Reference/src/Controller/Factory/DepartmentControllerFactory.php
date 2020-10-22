<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\DepartmentController;
use Reference\Form\DepartmentForm;
use Reference\Service\DepartmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class DepartmentControllerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(DepartmentService::class);
        $form = $container->get(DepartmentForm::class);
        return new DepartmentController($service, $form);
    }
}
