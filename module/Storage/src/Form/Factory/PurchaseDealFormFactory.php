<?php

namespace Storage\Form\Factory;

use Interop\Container\ContainerInterface;
use Storage\Form\PurchaseDealForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseDealFormFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        return new PurchaseDealForm($entityManager);
    }
}
