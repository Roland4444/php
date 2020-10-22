<?php

namespace Storage\Service;

use Interop\Container\ContainerInterface;
use Storage\Dao\PurchaseDealDao;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseDealServiceFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dao = $container->get(PurchaseDealDao::class);
        return new PurchaseDealService($dao);
    }
}
