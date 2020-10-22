<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\Spare;
use Spare\Service\SpareService;
use Zend\ServiceManager\Factory\FactoryInterface;

class SpareServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SpareService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Spare::class);
        $imageService = $container->get('imageService');
        return new SpareService($repository, $entityManager, $imageService);
    }
}
