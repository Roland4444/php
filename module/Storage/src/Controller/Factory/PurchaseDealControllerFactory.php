<?php

namespace Storage\Controller\Factory;

use Interop\Container\ContainerInterface;
use Storage\Controller\PurchaseDealController;
use Storage\Form\PurchaseDealForm;
use Storage\Service\PurchaseDealService;
use Storage\Service\PurchaseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseDealControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dealService = $container->get(PurchaseDealService::class);
        $purchaseService = $container->get(PurchaseService::class);
        $form = $container->get(PurchaseDealForm::class);
        return new PurchaseDealController($dealService, $purchaseService, $form);
    }
}
