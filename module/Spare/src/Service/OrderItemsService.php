<?php
namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Entity\Order;
use Spare\Entity\OrderItems;
use Spare\Repositories\OrderItemsRepository;

/**
 * Class OrderItemsService
 * @package Spare\Service
 * @method  OrderItemsRepository getRepository() Метод класса AbstractService
 */
class OrderItemsService extends AbstractService
{
    protected $entity = OrderItems::class;
    protected array $order = ['id' => 'DESC'];

    /**
     * Удаление заказов по переданным id
     *
     * @param $items
     * @throws
     */
    public function removeOrderItems($items)
    {
        if (! empty($items)) {
            foreach ($items as $item) { /**@var Order $item*/
                $this->getRepository()->remove($item->getId());
            }
        }
    }

    /**
     * Поиск броней по переданным позициям
     *
     * @param $positions
     * @return mixed|null
     */
    public function findByPositions($positions)
    {
        $arrayIds = [];
        foreach ($positions as $row) {
            $arrayIds[] = $row['orderItemId'];
        }

        $uniqueIds = array_unique($arrayIds);
        $orderItems = $this->getRepository()->findByPositions($uniqueIds);
        if (! empty($orderItems) && count($orderItems) == count($uniqueIds)) {
            return $orderItems;
        }

        return null;
    }

    public function findByPlanningItems($items)
    {
        $itemsIds = array_map(function ($o) {
            return $o->getId();
        }, $items->toArray());
        $ids = implode(',', $itemsIds);
        return $this->getRepository()->findByIds($ids);
    }

    /**
     * Удаляет старые записи
     *
     * @param Order $spareOrder
     * @return boolean
     */
    public function removeOldItems($spareOrder)
    {
        if (! empty($spareOrder->getId())) {
            foreach ($spareOrder->getItems() as $orderItem) {
                if (! empty($orderItem->getItemsReceipt()->count())) {
                    return false;
                }
            }

            $oldOrderItems = $spareOrder->getItems();
            $this->removeOrderItems($oldOrderItems);
        }

        return true;
    }
}
