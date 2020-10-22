<?php


namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\ContainerOwnerService;
use Reference\Service\ShipmentTariffService;
use Storage\Entity\Container;
use Storage\Service\ContainerItemService;
use Storage\Service\ContainerService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Container::class);
        $itemService = $container->get(ContainerItemService::class);
        $tariffService = $container->get(ShipmentTariffService::class);
        $ownerService = $container->get(ContainerOwnerService::class);
        return new ContainerService($repository, $itemService, $tariffService, $ownerService);
    }
}
