<?php


namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\Category;
use Reference\Entity\Role;
use Zend\ServiceManager\Factory\FactoryInterface;

class CategoryServiceFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Category::class);
        $roleRepository = $entityManager->getRepository(Role::class);

        return new CategoryService($repository, $roleRepository);
    }
}
