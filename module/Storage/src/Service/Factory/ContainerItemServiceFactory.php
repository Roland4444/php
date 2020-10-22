<?php

namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\MetalService;
use Storage\Entity\ContainerItem;
use Storage\Service\ContainerItemService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerItemServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(ContainerItem::class);
        $metalService = $container->get(MetalService::class);
        return new ContainerItemService($repository, $metalService);
    }
}
