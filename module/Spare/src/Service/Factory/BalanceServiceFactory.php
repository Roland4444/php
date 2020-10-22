<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Service\BalanceService;
use Zend\ServiceManager\Factory\FactoryInterface;

class BalanceServiceFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dao = $container->get('spareBalanceDao');
        return new BalanceService($dao);
    }
}
