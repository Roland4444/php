<?php


namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\ContainerOwnerService;
use Storage\Controller\ContainerController;
use Storage\Form\ContainerForm;
use Storage\Service\ContainerService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $containerService = $container->get(ContainerService::class);
        $accessValidateService = $container->get('accessValidateService');
        $containerOwnerService = $container->get(ContainerOwnerService::class);
        $form = $container->get(ContainerForm::class);
        return new ContainerController($containerService, $containerOwnerService, $accessValidateService, $form);
    }
}
