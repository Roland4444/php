<?php

namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Reference\Entity\Settings;
use Zend\ServiceManager\Factory\FactoryInterface;

class SettingsServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Settings::class);
        return new SettingsService($repository);
    }
}
