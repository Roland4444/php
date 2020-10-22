<?php


namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\Department;
use Zend\ServiceManager\Factory\FactoryInterface;

class DepartmentServiceFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Department::class);
        return new DepartmentService($repository);
    }
}
