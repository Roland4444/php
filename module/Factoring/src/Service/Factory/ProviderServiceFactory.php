<?php
namespace Factoring\Service\Factory;

use Factoring\Entity\Provider;
use Factoring\Service\ProviderService;
use Interop\Container\ContainerInterface;

class ProviderServiceFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Provider::class);
        return new ProviderService($repository);
    }
}
