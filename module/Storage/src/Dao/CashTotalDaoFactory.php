<?php

namespace Storage\Dao;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CashTotalDaoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new CashTotalDao($container->get('entityManager'));
    }
}
