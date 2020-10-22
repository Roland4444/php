<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Spare\Controller\TotalController;
use Reference\Service\WarehouseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TotalControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TotalController
     * @throws
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $services = [
            'spareTotalService' => $container->get('spareTotalService'),
        ];
        $accessValidateService = $container->get('accessValidateService');
        $entityManager = $container->get('entityManager');
        return new TotalController($entityManager, $services, $accessValidateService);
    }
}
