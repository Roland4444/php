<?php

namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Controller\WeighingController;
use Storage\Service\WeighingService;
use Zend\ServiceManager\Factory\FactoryInterface;

class WeighingControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $weighingService = $container->get(WeighingService::class);
        $config = $container->get('Config');
        $weighingDir = $config['weighing_dir'];
        $entityManager = $container->get('entityManager');

        $services = [
            DepartmentService::class => $container->get(DepartmentService::class),
            CustomerService::class => $container->get(CustomerService::class)
        ];
        return new WeighingController($weighingService, $weighingDir, $entityManager, $services);
    }
}
