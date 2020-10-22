<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Reference\Form\ShipmentTariffForm;
use Reference\Service\ShipmentTariffService;

class ShipmentTariffController extends AbstractReferenceController
{
    use FilterableController;
    /**
     * ShipmentTariffController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, ShipmentTariffService::class, ShipmentTariffForm::class);
        $this->routeIndex = "shipmentTariff";
    }
}
