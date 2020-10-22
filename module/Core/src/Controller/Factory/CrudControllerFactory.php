<?php

namespace Core\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

abstract class CrudControllerFactory implements FactoryInterface
{
    protected $service;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get($this->service);
        $services = [
            DepartmentService::class => $container->get(DepartmentService::class),
        ];

        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
