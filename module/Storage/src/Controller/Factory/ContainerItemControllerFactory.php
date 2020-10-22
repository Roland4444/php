<?php

namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Storage\Controller\ContainerItemController;
use Storage\Form\ContainerItemForm;
use Storage\Service\ContainerItemService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerItemControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $itemService = $container->get(ContainerItemService::class);
        $accessValidateService = $container->get('accessValidateService');
        $form = $container->get(ContainerItemForm::class);
        return new ContainerItemController($itemService, $form, $accessValidateService);
    }
}
