<?php

namespace Storage\Service\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Entity\Weighing;
use Storage\Service\WeighingService;
use Zend\ServiceManager\Factory\FactoryInterface;

class WeighingServiceFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Weighing::class);
        $departmentService = $container->get(DepartmentService::class);
        $customerService = $container->get(CustomerService::class);

        $weighingItemServiceFactory = new WeighingItemServiceFactory();
        $weighingItemService = $weighingItemServiceFactory->__invoke($container, 'weighingItemService');
        $imageService = $container->get('imageService');
        $config = $container->get('Config');
        $weighingDir = $config['weighing_dir'];

        return new WeighingService($repository, $weighingItemService, $departmentService, $customerService, $imageService, $weighingDir);
    }
}
