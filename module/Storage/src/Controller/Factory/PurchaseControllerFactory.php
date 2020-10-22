<?php

namespace Storage\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Interop\Container\ContainerInterface;
use Reports\Service\RemoteSkladService;
use Storage\Form\PurchaseForm;
use Storage\Service\WeighingService;
use Storage\Service\PurchaseDealService;
use Storage\Service\PurchaseService;

/**
 * Class PurchaseControllerFactory
 * @package Storage\Controller\Factory
 */
class PurchaseControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $purchaseService = $container->get(PurchaseService::class);
        $services = [
            PurchaseDealService::class => $container->get(PurchaseDealService::class),
            RemoteSkladService::class => $container->get('RemoteSkladService'),
            WeighingService::class => $container->get(WeighingService::class)
        ];
        $purchaseForm = $container->get(PurchaseForm::class);
        return new $requestedName($entityManager, $purchaseService, $services, $purchaseForm);
    }
}
