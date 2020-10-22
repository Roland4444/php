<?php

namespace Reference\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Controller\SettingsController;
use Reference\Form\SettingsForm;
use Reference\Service\SettingsService;
use Zend\ServiceManager\Factory\FactoryInterface;

class SettingsControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(SettingsService::class);
        $form = $container->get(SettingsForm::class);
        return new SettingsController($service, $form);
    }
}
