<?php

namespace Reports\Dao;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RemoteSkladDaoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new RemoteSkladDao($container->get('entityManager'));
    }
}
