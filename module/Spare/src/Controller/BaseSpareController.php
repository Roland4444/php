<?php

namespace Spare\Controller;

use Exception;
use Spare\Service\InventoryService;
use Zend\Mvc\Controller\AbstractActionController;

abstract class BaseSpareController extends AbstractActionController
{
    /** @var InventoryService */
    protected $inventoryService;

    protected function validateIfNoLaterInventory(int $warehouseId, string $date)
    {
        $itemExceedingDateExist = $this->inventoryService->checkExistsItemExceedingDate($warehouseId, $date);

        if ($itemExceedingDateExist) {
            throw new Exception('По складу проводились более поздние операции инвентаризации');
        }
    }
}
