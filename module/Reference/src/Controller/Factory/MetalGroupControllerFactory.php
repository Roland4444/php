<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\MetalGroupController;
use Reference\Form\MetalGroupForm;
use Reference\Service\MetalGroupService;
use Zend\ServiceManager\Factory\FactoryInterface;

class MetalGroupControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(MetalGroupService::class);
        $form = $container->get(MetalGroupForm::class);
        return new MetalGroupController($service, $form);
    }
}
