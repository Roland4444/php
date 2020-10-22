<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\ContainerOwnerController;
use Reference\Form\ContainerOwnerForm;
use Reference\Service\ContainerOwnerService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerOwnerControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(ContainerOwnerService::class);
        $form = $container->get(ContainerOwnerForm::class);
        return new ContainerOwnerController($service, $form);
    }
}
