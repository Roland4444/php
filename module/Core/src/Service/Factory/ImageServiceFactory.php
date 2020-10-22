<?php

namespace Core\Service\Factory;

use Core\Entity\Image;
use Core\Service\ImageService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ImageServiceFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Image::class);
        $config = $container->get('Config');
        return new ImageService($repository, $config['uploads_dir']);
    }
}
