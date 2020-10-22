<?php

namespace Modules\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Modules\Entity\TransportIncas;
use Modules\Service\TransportIncasService;

class TransportIncasServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(TransportIncas::class);
        return new TransportIncasService($repository);
    }
}
