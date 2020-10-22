<?php

namespace Storage\Dao;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseDealDaoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PurchaseDealDao($container->get('entityManager'));
    }
}
