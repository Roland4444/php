<?php

namespace Modules\Controller\Factory;

use Interop\Container\ContainerInterface;
use Modules\Controller\IncasController;
use Modules\Service\TransportIncasService;
use Zend\ServiceManager\Factory\FactoryInterface;

class IncasControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(TransportIncasService::class);
        return new IncasController($service);
    }
}
