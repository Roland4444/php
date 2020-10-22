<?php

namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\Metal;
use Zend\ServiceManager\Factory\FactoryInterface;

class MetalServiceFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Metal::class);
        return new MetalService($repository);
    }
}
