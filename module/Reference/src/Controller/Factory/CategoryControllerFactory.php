<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\CategoryController;
use Reference\Form\CategoryForm;
use Reference\Service\CategoryService;
use Zend\ServiceManager\Factory\FactoryInterface;

class CategoryControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(CategoryService::class);
        $form = $container->get(CategoryForm::class);
        return new CategoryController($service, $form);
    }
}
