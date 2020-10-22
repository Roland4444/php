<?php

namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\MetalService;
use Storage\Entity\WeighingItem;
use Storage\Service\WeighingItemService;
use Zend\ServiceManager\Factory\FactoryInterface;

class WeighingItemServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(WeighingItem::class);

        $metalService = $container->get(MetalService::class);
        $config = $container->get("Config");

        return new WeighingItemService($repository, $metalService, $config);
    }
}
