<?php

namespace Storage\Form\Factory;

use Interop\Container\ContainerInterface;
use Reference\Service\CustomerService;
use Reference\Service\MetalService;
use Storage\Form\PurchaseForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseFormFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $customerService = $container->get(CustomerService::class);
        $metalService = $container->get(MetalService::class);
        $entityManager = $container->get('entityManager');
        return new PurchaseForm($entityManager, $customerService, $metalService);
    }
}
