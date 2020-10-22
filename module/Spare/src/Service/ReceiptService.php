<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Exception;
use Spare\Entity\OrderItems;
use Spare\Entity\Receipt;
use Spare\Entity\ReceiptItems;
use Spare\Entity\Seller;
use Spare\Repositories\ReceiptRepository;

/**
 * Class ReceiptService
 * @package Spare\Service
 * @method  ReceiptRepository getRepository() Метод класса AbstractService
 */
class ReceiptService extends AbstractService
{
    protected $entity = Receipt::class;
    protected array $order = ['id' => 'DESC'];

    /**
     * Поиск приходов для индексной страницы
     *
     * @param $params
     * @param int $warehouseId
     * @return mixed
     */
    public function findByParams($params, int $warehouseId)
    {
        return $this->getRepository()->findByParams($params, $warehouseId);
    }

    /**
     * Наполнение данными прихода
     *
     * @param array   $orderItems
     * @param array   $positions
     * @param Receipt $spareReceipt
     */
    public function fillingReceipt($orderItems, $positions, $spareReceipt)
    {
        $spareReceipt->clearItems();

        foreach ($positions as $position) {
            $orderItemId = $position['orderItemId'];
            if (! isset($orderItems[$orderItemId])) {
                continue;
            }
            $orderItem = $orderItems[$orderItemId];
            $spareReceiptItems = new ReceiptItems();
            $spareReceiptItems->setQuantity(abs($position['count']));
            $spareReceiptItems->setOrderItem($orderItem);
            $spareReceiptItems->setSpare($orderItem->getSpare());
            $spareReceiptItems->setReceipt($spareReceipt);
            if (! empty($orderItem->getSubQuantity())) {
                $spareReceiptItems->setSubQuantity($orderItem->getSubQuantity());
            }

            $spareReceipt->addItem($spareReceiptItems);
        }
    }

    /**
     * Подготовка полученных данных для сохранения
     *
     * @param array   $orderItems
     * @param array   $positions
     * @return array
     */
    public function getIdsForUpdate($orderItems, $positions)
    {
        $planningItemIds = [];
        $orderIds = [];

        foreach ($positions as $position) {
            $orderItemId = $position['orderItemId'];
            if (! isset($orderItems[$orderItemId])) {
                continue;
            }
            $orderItem = $orderItems[$orderItemId];

            if (! empty($orderItem->getPlanningItem())) {
                $planningItemIds[] = $orderItem->getPlanningItem()->getId();
            }

            $orderIds[] = $orderItem->getOrder()->getId();
        }

        return [array_unique($planningItemIds), array_unique($orderIds)];
    }

    /**
     * Проверка полученных данных
     *
     * @param $positions
     * @return bool
     */
    public function isValidPositions($positions)
    {
        foreach ($positions as $row) {
            if (empty($row['count']) || empty((int)$row['orderItemId'])
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверяет, что в приходе используется брони только от одного поставщика. Возвращает его
     *
     * @param $orderItems
     * @return Seller
     * @throws Exception
     */
    public function getSeller($orderItems)
    {
        $sellers = [];
        /**@var OrderItems $item*/
        foreach ($orderItems as $item) {
            $sellers[$item->getOrder()->getSeller()->getId()] = $item->getOrder()->getSeller();
        }
        if (count($sellers) > 1) {
            throw new Exception('В одном приходе не может быть передано более одного поставщика');
        }
        return array_shift($sellers);
    }

    /**
     * @param Receipt $spareReceipt
     * @return array
     */
    public function getDateForUpdateStatus(Receipt $spareReceipt)
    {
        $oldReceiptItems = $spareReceipt->getItems();
        $idsPlanningItemsOld = [];
        $idsOrderOld = [];
        foreach ($oldReceiptItems as $oldReceiptItem) {/**@var ReceiptItems $oldReceiptItem */
            if (! empty($oldReceiptItem->getOrderItem()->getPlanningItem())) {
                $idsPlanningItemsOld[] = $oldReceiptItem->getOrderItem()->getPlanningItem()->getId();
            }
            $idsOrderOld[] = $oldReceiptItem->getOrderItem()->getOrder()->getId();
        }

        return [array_unique($idsPlanningItemsOld), array_unique($idsOrderOld)];
    }


    /**
     * Возвращает список поступлений по заданному складу с учетом даты последней инвентаризации
     *
     * @param $warehouseId
     * @param $dateStart
     * @return array
     */
    public function getTotalReceipt($warehouseId, $dateStart)
    {
        $receipts = $this->getRepository()->getTotalReceipt($warehouseId, $dateStart);
        if (empty($receipts)) {
            return [];
        }
        $result = [];
        foreach ($receipts as $receipt) {
            /**@var Receipt $receipt */
            foreach ($receipt->getItems() as $item) {
                /**@var ReceiptItems $item */
                $spareId = $item->getSpare()->getId();

                $quantity = $item->getSubQuantity()
                    ? $item->getSubQuantity() * $item->getQuantity()
                    : $item->getQuantity();

                if (! isset($result[$spareId])) {
                    $result[$spareId] = [
                        'spare_id' => $spareId,
                        'spareUnits' => $item->getSpare()->getUnits(),
                        'total' => $quantity,
                        'text' => $item->getSpare()->getName(),
                    ];
                } else {
                    $result[$spareId]['total'] += $quantity;
                }
            }
        }
        return $result;
    }

    /**
     * Получить список поступлений на определенную дату
     * @param int $warehouseId
     * @param $date
     * @return mixed
     */
    public function getReceiptByDate(int $warehouseId, $date)
    {
        return $this->getRepository()->findBy([
            'date' => $date,
            'warehouse' => $warehouseId
        ]);
    }
}
