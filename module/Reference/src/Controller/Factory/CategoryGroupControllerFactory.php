<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\CategoryGroupController;
use Reference\Form\CategoryGroupForm;
use Reference\Service\CategoryGroupService;
use Zend\ServiceManager\Factory\FactoryInterface;

class CategoryGroupControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(CategoryGroupService::class);
        $form = $container->get(CategoryGroupForm::class);
        return new CategoryGroupController($service, $form);
    }
}
