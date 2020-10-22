<?php

namespace Storage\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\MetalService;
use Storage\Form\ContainerItemForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerItemFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $metalService = $container->get(MetalService::class);
        return new ContainerItemForm($entityManager, $metalService);
    }
}
