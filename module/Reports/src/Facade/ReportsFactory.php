<?php

namespace Reports\Facade;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ReportsFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $officeCash = $container->get('officeCash');
        return new Reports($officeCash);
    }
}
