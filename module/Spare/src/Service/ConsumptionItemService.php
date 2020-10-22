<?php


namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Entity\Consumption;
use Spare\Entity\ConsumptionItem;

class ConsumptionItemService extends AbstractService
{
    private $vehicleService;
    private $spareService;

    public function __construct($repository, $vehicleService, $spareService)
    {
        parent::__construct($repository);
        $this->vehicleService = $vehicleService;
        $this->spareService = $spareService;
    }

    public function fill(Consumption $consumption, $consumptionItemData): ConsumptionItem
    {
        $consumptionItemEntity = new ConsumptionItem();
        if (isset($consumptionItemData->id)) {
            $consumptionItemEntity = $this->getReference($consumptionItemData->id);
        }
        $consumptionItemEntity->setComment($consumptionItemData->comment);
        $consumptionItemEntity->setQuantity($consumptionItemData->quantity);

        if (! empty($consumptionItemData->vehicle->value)) {
            $consumptionItemEntity->setVehicle(
                $this->vehicleService->getReference($consumptionItemData->vehicle->value)
            );
        }

        $consumptionItemEntity->setSpare(
            $this->spareService->getReference($consumptionItemData->spare->value)
        );
        $consumptionItemEntity->setConsumption($consumption);

        return $consumptionItemEntity;
    }
}
