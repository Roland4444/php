<?php

namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\Warehouse;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return WarehouseService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Warehouse::class);
        $userService = $container->get(UserService::class);
        return new WarehouseService($repository, $userService);
    }
}
