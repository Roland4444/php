<?php

namespace Storage\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Interop\Container\ContainerInterface;
use Reference\Service\ShipmentTariffService;
use Reference\Service\TraderService;
use Storage\Controller\ShipmentController;
use Storage\Form\ShipmentForm;
use Storage\Service\ShipmentService;

/**
 * Class ShipmentControllerFactory
 * @package Storage\Controller\Factory
 */
class ShipmentControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $shipmentService = $container->get(ShipmentService::class);
        $accessValidateService = $container->get('accessValidateService');
        $traderService = $container->get(TraderService::class);
        $tariffService = $container->get(ShipmentTariffService::class);
        $form = $container->get(ShipmentForm::class);
        return new ShipmentController($entityManager, $shipmentService, $accessValidateService, $traderService, $tariffService, $form);
    }
}
