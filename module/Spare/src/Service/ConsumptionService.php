<?php

namespace Spare\Service;

use Core\Service\AbstractService;
use Doctrine\ORM\ORMException;
use Exception;
use Reference\Service\EmployeeService;
use Reference\Service\WarehouseService;
use Spare\Entity\Consumption;
use Spare\Entity\ConsumptionItem;
use Spare\Entity\Inventory;
use Spare\Repositories\ConsumptionRepository;
use Zend\Form\Form;

/**
 * Class ConsumptionService
 * @package Spare\Service
 * @method  ConsumptionRepository getRepository() Метод класса AbstractService
 */
class ConsumptionService extends AbstractService
{
    protected $warehouseService;
    protected $consumptionItemService;
    protected $employeeService;
    protected $inventoryService;

    /**
     * ConsumptionService constructor.
     * @param ConsumptionRepository $repository
     * @param ConsumptionItemService $consumptionItemService
     * @param EmployeeService $employeeService
     * @param WarehouseService $warehouseService
     * @param InventoryService $inventoryService
     */
    public function __construct($repository, $consumptionItemService, $employeeService, $warehouseService, $inventoryService)
    {
        parent::__construct($repository);
        $this->consumptionItemService = $consumptionItemService;
        $this->employeeService = $employeeService;
        $this->warehouseService = $warehouseService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Поиск расходов для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    public function findConsumptions($params)
    {
        return $this->getRepository()->findConsumptions($params);
    }

    /**
     * Сохранение / обновление расходов
     *
     * @param array $data
     * @throws ORMException
     * @throws Exception
     */
    public function store(array $data)
    {
        $consumptionEntity = $this->fill($data);

        $this->getRepository()->save($consumptionEntity);
    }

    public function fill(array $data): Consumption
    {
        $consumptionEntity = new Consumption();

        if (! empty($data['consumptionId'])) {
            $consumptionEntity = $this->getReference($data['consumptionId']);
        }
        $consumptionEntity->setDate($data['date']);
        $consumptionEntity->setEmployee(
            $this->employeeService->getReference($data['employee'])
        );
        $consumptionEntity->setWarehouse(
            $this->warehouseService->getReference($data['warehouse_id'])
        );

        foreach (json_decode($data['positions']) as $consumptionItemData) {
            $consumptionItemEntity = $this->consumptionItemService->fill($consumptionEntity, $consumptionItemData);
            $consumptionEntity->addItem($consumptionItemEntity);
        }

        return $consumptionEntity;
    }

    /**
     * Возвращает список расходов по заданному складу с учетом даты последней инвентаризации
     *
     * @param $warehouseId
     * @param $dateStart
     * @return array
     */
    public function getTotalConsumption($warehouseId, $dateStart)
    {
        $consumptions = $this->getRepository()->getTotalConsumption($warehouseId, $dateStart);
        if (empty($consumptions)) {
            return [];
        }

        $totalConsumption = [];


        foreach ($consumptions as $consumption) {
            $totalConsumption = array_merge($totalConsumption, $this->countItemsInSingleConsumption($consumption));
        }

        return $this->sumConsumptionItems($totalConsumption);
    }

    /**
     * Подсчитать кол-во запчастей в одном заказе
     * @param Consumption $consumption
     * @return array
     */
    private function countItemsInSingleConsumption(Consumption $consumption)
    {
        $result = [];

        foreach ($consumption->getItems() as $consumptionItem) {
            /**@var ConsumptionItem $consumptionItem */
            $spareId = $consumptionItem->getSpare()->getId();
            if (! isset($result[$spareId])) {
                $result[$spareId] = [
                    'spare_id' => $spareId,
                    'spareUnits' => $consumptionItem->getSpare()->getUnits(),
                    'total' => $consumptionItem->getQuantity(),
                    'text' => $consumptionItem->getSpare()->getName(),
                ];
            } else {
                $result[$spareId]['total'] += $consumptionItem->getQuantity();
            }
        }

        return $result;
    }

    private function sumConsumptionItems(array $totalConsumption)
    {
        $resultedArray = [];

        foreach ($totalConsumption as $item) {
            if (! isset($resultedArray[$item['spare_id']])) {
                    $resultedArray[$item['spare_id']] = [
                        'spare_id' => $item['spare_id'],
                        'spareUnits' => $item['spareUnits'],
                        'total' => $item['total'],
                        'text' => $item['text']
                    ];
            } else {
                $resultedArray[$item['spare_id']] = [
                    'spare_id' => $item['spare_id'],
                    'spareUnits' => $item['spareUnits'],
                    'total' => $item['total'] + $resultedArray[$item['spare_id']]['total'],
                    'text' => $item['text']
                ];
            }
        }

        return $resultedArray;
    }

    /**
     * Получить список поступлений на определенную дату
     * @param int $warehouseId
     * @param $date
     * @return mixed
     */
    public function getConsumptionsByDate(int $warehouseId, $date)
    {
        return $this->getRepository()->findBy([
            'date' => $date,
            'warehouse' => $warehouseId
        ]);
    }

    public function validateSave(array $data)
    {
        if (! $this->validateForm($data)) {
            return false;
        }

        $consumptionItems = json_decode($data['positions'], true);

        if (! $this->validateConsumptionNotExceedingTotals($consumptionItems, $data['totals'])) {
            return false;
        }

        return true;
    }

    public function validateUpdate(array $data, $consumption)
    {
        if (! $this->validateForm($data)) {
            return false;
        }

        $consumptionItems = json_decode($data['positions'], true);

        // Пересчитать totals
        /** @var ConsumptionItem $updatableConsumptionItem */
        foreach ($consumption->getItems() as $updatableConsumptionItem) {
            $totalSpareIndex = $updatableConsumptionItem->getSpare()->getId();
            if ($updatableConsumptionItem->getSpare()->getId() == $data['totals'][$totalSpareIndex]['spare_id']) {
                $data['totals'][$totalSpareIndex]['total'] = $data['totals'][$totalSpareIndex]['total'] + $updatableConsumptionItem->getQuantity();
            }
        }

        if (! $this->validateConsumptionNotExceedingTotals($consumptionItems, $data['totals'])) {
            return false;
        }

        return true;
    }

    private function validateForm(array $data)
    {
        $consumption = new Consumption();
        $consumptionForm = new Form();
        $consumptionForm->setInputFilter($consumption->getInputFilter());
        $consumptionForm->setData($data);

        return $consumptionForm->isValid();
    }

    private function validateConsumptionNotExceedingTotals(array $consumptionItems, array $totals)
    {
        foreach ($consumptionItems as $consumptionItem) {
            foreach ($totals as $totalItem) {
                if ($consumptionItem['spare']['value'] == $totalItem['spare_id'] && $consumptionItem['quantity'] > $totalItem['total']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Consumption[] $consumptions
     * @return int|mixed
     */
    public function getQuantity($consumptions)
    {
        $total = 0;

        foreach ($consumptions as $consumption) {
            foreach ($consumption->getItems() as $consumptionItems) {
                $total += $consumptionItems->getQuantity();
            }
        }

        return $total;
    }
}
