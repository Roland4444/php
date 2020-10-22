<?php

namespace Spare\Facade;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SpareFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $officeCash = $container->get('officeCash');
        return new Spare($officeCash);
    }
}
