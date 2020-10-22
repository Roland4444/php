<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Reference\Service\WarehouseService;
use Spare\Entity\Inventory;
use Spare\Entity\InventoryItems;
use Spare\Repositories\InventoryRepository;
use \Zend\Stdlib\Parameters;

/**
 * Class InventoryService
 * @package Spare\Service
 * @method  InventoryRepository getRepository() Метод класса AbstractService
 */
class InventoryService extends AbstractService
{
    private $spareService;
    private $warehouseService;

    public function __construct(InventoryRepository $inventoryRepository, SpareService $spareService, WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
        $this->spareService = $spareService;
        parent::__construct($inventoryRepository);
    }

    /**
     * @param Parameters $request
     * @return array|null
     */
    public function getPositions(Parameters $request)
    {
        $positionsJson = $request->get('positions');
        if (empty($positionsJson)) {
            return null;
        }

        $positions = json_decode($positionsJson, true);
        if (empty($positions)) {
            return null;
        }

        return $this->preparePositions($positions);
    }

    /**
     * Проверка сохранения
     *
     * @param $positions
     * @return array|null
     */
    protected function preparePositions($positions)
    {
        $finishedPositions = [];
        foreach ($positions as $row) {
            $spareId = $row['spareId'];
            if (empty($spareId)) {
                return null;
            }
            $finishedPositions[$spareId] = $row;
        }
        return $finishedPositions;
    }

    public function getInventory($params, int $warehouseId)
    {
        if ($params['inventoryId']) {
            $inventory = $this->find($params['inventoryId']);
            if (empty($inventory)) {
                return null;
            }
            if ($warehouseId != $inventory->getWarehouse()->getId()) {
                return null;
            }
        } else {
            $inventory = new Inventory();
            $warehouse = $this->warehouseService->getReference($warehouseId);
            $inventory->setWarehouse($warehouse);
        }

        $inventory->setDate($params['date']);

        return $inventory;
    }

    /**
     * @param $positions
     * @param $inventory
     * @return mixed
     */
    public function fillingItems($positions, Inventory $inventory)
    {
        foreach ($inventory->getItems() as $key => $item) {
            /**@var InventoryItems $item*/
            $spareId = $item->getSpare()->getId();
            if (! isset($positions[$spareId])) {
                $inventory->removeItem($key);
            } else {
                $item->setQuantityFact($positions[$spareId]['totalFact']);
                $item->setQuantityFormal($positions[$spareId]['totalFormal']);
                unset($positions[$spareId]);
            }
        }

        if (empty($positions)) {
            return $inventory;
        }

        return $this->addingInventoryPositions($positions, $inventory);
    }

    public function addingInventoryPositions($positions, Inventory $inventory)
    {
        foreach ($positions as $position) {
            $spareId = $position['spareId'];
            $item = new InventoryItems();
            $item->setQuantityFact($position['totalFact']);
            $item->setQuantityFormal($position['totalFormal']);
            $spare = $this->spareService->getReference($spareId);
            $item->setSpare($spare);
            $item->setInventory($inventory);
            $inventory->addItem($item);
        }

        return $inventory;
    }

    public function getTableListData($params)
    {
        return $this->getRepository()->getTableListData($params);
    }

    public function isLastInventory($warehouseId, $id) :bool
    {
        $lastInventory = $this->getLastInventory($warehouseId);
        if (empty($lastInventory)) {
            return true;
        }
        return $id == $lastInventory->getId();
    }

    public function isNextDate($warehouseId, $date) :bool
    {
        if (empty($date)) {
            return false;
        }

        $lastInventory = $this->getLastInventory($warehouseId);
        if (empty($lastInventory)) {
            return true;
        }

        return  strtotime($date) > strtotime($lastInventory->getDate());
    }

    /**
     * @param $warehouseId
     * @return null|Inventory
     */
    public function getLastInventory($warehouseId): ?Inventory
    {
        return $this->getRepository()->getLastInventory($warehouseId);
    }

    public function getTotalsInventory($warehouseId)
    {
        $lastInventory = $this->getLastInventory($warehouseId);

        if (empty($lastInventory)) {
            return [];
        }

        $result = ['dateStart' => $lastInventory->getDate()];

        foreach ($lastInventory->getItems() as $item) {
            /**@var \Spare\Entity\InventoryItems $item*/
            $spareId = $item->getSpare()->getId();
            $result['spares'][$spareId] = [
                'spare_id' => $spareId,
                'spareUnits' => $item->getSpare()->getUnits(),
                'text' => $item->getSpare()->getName(),
                'total' => $item->getQuantityFact(),
            ];
        }

        return $result;
    }

    public function checkExistsItemExceedingDate(int $warehouseId, string $date): bool
    {
        $item = $this->getRepository()->getItemExceedingDate($warehouseId, $date);

        return $item != null;
    }
}
