<?php


namespace Spare\Dao;

use Interop\Container\ContainerInterface;
use Spare\Service\BalanceService;
use Zend\ServiceManager\Factory\FactoryInterface;

class BalanceDaoFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new BalanceDao($entityManager);
    }
}
