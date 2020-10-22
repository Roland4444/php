<?php

namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Entity\Order;
use Spare\Entity\OrderItems;
use Spare\Entity\OrderPaymentStatus;
use Spare\Entity\OrderStatus;
use Spare\Entity\PlanningItems;
use Spare\Repositories\OrderRepository;

/**
 * Class OrderService
 * @package Spare\Service
 * @method  OrderRepository getRepository() Метод класса AbstractService
 */
class OrderService extends AbstractService
{
    private $orderStatusService;
    private $orderPaymentStatusService;

    public function __construct(
        $repository,
        OrderStatusService $orderStatusService,
        OrderPaymentStatusService $orderPaymentStatusService
    ) {
        $this->orderStatusService = $orderStatusService;
        $this->orderPaymentStatusService = $orderPaymentStatusService;
        parent::__construct($repository);
    }


    /**
     * Поиск броней для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    public function findOrders($params)
    {
        return $this->getRepository()->getOrders($params);
    }

    /**
     * Итоговая сумма по всем заказам
     * @param Order[] $orders
     * @return int
     */
    public function getTotalAmount($orders)
    {
        $total = 0;
        foreach ($orders as $order) {
            $total += $order->getPrice();
        }

        return $total;
    }

    /**
     * Проверка сохранения
     *
     * @param $positions
     * @return bool
     */
    public function isValidPositions($positions)
    {
        foreach ($positions as $row) {
            if (($row['isComposite'] && ! $row['subCount'])
                || empty((float)$row['count'])
                || empty((int)$row['planningItemId'])
                || empty((float)$row['price'])
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Подготовка данных заказа для передачи на фронт и дальнейшего изменения
     *
     * @param Order $spareOrder
     *
     * @return array
     */
    //todo название говно. переименовать метод.
    public function getDataFromOrder(Order $spareOrder)
    {
        $order = [
            'id' => $spareOrder->getId(),
            'date' => $spareOrder->getDate(),
            'expectedDate' => $spareOrder->getExpectedDate(),
            'documentName' => $spareOrder->getDocument(),
            'provider' => $spareOrder->getSeller()->getId(),
        ];

        foreach ($spareOrder->getItems() as $orderItem) { /**@var OrderItems $orderItem*/
            $planningItem = $orderItem->getPlanningItem();

            $planning = $planningItem->getPlanning();

            $order['data'][] = [
                'itemId' => $orderItem->getId(),
                'planningItemId' => $planningItem->getId(),
                'nameSpare' => $orderItem->getSpare()->getName(),
                'spareUnits' => $orderItem->getSpare()->getUnits(),
                'spareId' => $orderItem->getSpare()->getId(),
                'count' => $orderItem->getQuantity(),
                'countInPlanning' => $planningItem->getQuantity(),
                'date' => $planning->getDate(),
                'number' => $planning->getId(),
                'isComposite' => $orderItem->getSpare()->getIsComposite(),
                'subCount' => $orderItem->getSubQuantity(),
                'price' => $orderItem->getPrice(),
                'countRestForOrder' => $planningItem->getQuantity() - $planningItem->getOrdered() + $orderItem->countInOrder(),
            ];
        }
        return $order;
    }

    public function getByPeriodAndSellerId(string $dateFrom, string $dateTo, int $sellerId = null): array
    {
        return $this->getRepository()->getByPeriodAndSellerId($dateFrom, $dateTo, $sellerId);
    }

    /**
     * Меняет статус закупок по переданому массиву состоящих из id orderItems
     *
     * @param $itemIds
     * @throws
     */
    public function updateStatusOrderByIds($itemIds)
    {
        $statuses = $this->orderStatusService->getOrderStatuses();
        $orders = $this->getOrdersByIds($itemIds);
        if (empty($orders)) {
            return;
        }

        $dateForUpdate = [];
        foreach ($orders as $order) {/**@var Order $order */
            $countCloseItem = 0;
            $status = $statuses[OrderStatus::ALIAS_IN_WORK]->getId();

            foreach ($order->getItems() as $orderItem) { /**@var OrderItems $orderItem */
                $quantity = $orderItem->getSubQuantity()
                    ? $orderItem->getSubQuantity() * $orderItem->getQuantity()
                    : $orderItem->getQuantity();
                if ($orderItem->getReceipted() >= $quantity) {
                    $countCloseItem++;
                }
            }

            if ($order->getItems()->count() == $countCloseItem) {
                $status = $statuses[OrderStatus::ALIAS_CLOSED]->getId();
            }

            $dateForUpdate[$status][] = $order->getId();
        }

        if (empty($dateForUpdate)) {
            return;
        }

        foreach ($dateForUpdate as $status => $rows) {
            $ids = implode(',', $rows);
            $this->getRepository()->updateStatus($ids, $status);
        }
    }

    /**
     * Производит обновление статуса оплаты в заказе по переданном id
     *
     * @param $orderId
     */
    public function updatePaymentStatusOrderByIds($orderId)
    {
        $order = $this->getRepository()->findByIdForUpdatePaymentStatus($orderId);
        if (empty($order)) {
            return;
        }
        $statuses = $this->orderPaymentStatusService->getOrderStatuses();

        $order->setPaymentStatus($statuses[OrderPaymentStatus::NOT_PAYMENT]);

        $orderPrice = $order->getPrice();
        $sumInExpenses = 0;
        foreach ($order->getExpenses() as $expense) {
            $sumInExpenses += $expense->getMoney();
        }

        if ($sumInExpenses > 0) {
            $order->setPaymentStatus($statuses[OrderPaymentStatus::PARTIALLY_PAID]);
        }

        if ($sumInExpenses >= $orderPrice) {
            $order->setPaymentStatus($statuses[OrderPaymentStatus::PAID]);
        }

        $this->save($order);
    }

    /**
     * Производит обновление статуса оплаты в заказе по переданном id
     *
     * @param $orderId
     */
    public function updatePaymentCashStatusOrderByIds($orderId)
    {
        $order = $this->getRepository()->findByIdForUpdatePaymentStatus($orderId);
        if (empty($order)) {
            return;
        }
        $statuses = $this->orderPaymentStatusService->getOrderStatuses();

        $order->setPaymentStatus($statuses[OrderPaymentStatus::NOT_PAYMENT]);

        $orderPrice = $order->getPrice();
        $sumInExpenses = 0;
        foreach ($order->getCashExpenses() as $expense) {
            $sumInExpenses += $expense->getMoney();
        }

        if ($sumInExpenses > 0) {
            $order->setPaymentStatus($statuses[OrderPaymentStatus::PARTIALLY_PAID]);
        }

        if ($sumInExpenses >= $orderPrice) {
            $order->setPaymentStatus($statuses[OrderPaymentStatus::PAID]);
        }

        $this->save($order);
    }

    /**
     * Возвращает закупки по переданным item_id
     *
     * @param array $itemIds
     * @return mixed
     */
    protected function getOrdersByIds($itemIds)
    {
        if (empty($itemIds)) {
            return null;
        }
        $ids = implode(',', $itemIds);

        return $this->getRepository()->getOrdersByIds($ids);
    }

    /**
     * Возвращает закупки по переданным expense_id
     *
     * @param array $ids
     * @return mixed
     */
    public function getOrdersByExpenseIds($ids)
    {
        return $this->getRepository()->getOrdersByExpenseIds($ids);
    }

    /**
     * Получение статуса по его алиасу
     *
     * @param $alias
     * @return mixed|null
     */
    public function getStatusByAlias($alias)
    {
        return $this->orderStatusService->getStatusByAlias($alias);
    }

    /**
     * Получение статуса оплаты по его алиасу
     *
     * @param $alias
     * @return mixed|null
     */
    public function getPaymentStatusByAlias($alias)
    {
        return $this->orderPaymentStatusService->getStatusByAlias($alias);
    }

    /**
     * Поиск неоплаченных броней для привязки оплаты
     *
     * @param null $sellerInn
     * @return mixed
     */
    public function getNotPaid($sellerInn = null)
    {
        $orders = $this->getRepository()->getNotPaid($sellerInn);

        if (empty($orders)) {
            return [];
        }

        $result = [];
        foreach ($orders as $order) {
            $inn = $order->getSeller()->getInn();
            $id = $order->getId();
            $result[$inn][$id] = [
                'id' => $id,
                'document' => $order->getDocument(),
                'price' => $order->getPrice(),
                'date' => $order->getDate(),
            ];

            foreach ($order->getItems() as $item) {
                $result[$inn][$id]['items'][] = [
                    'spare' => $item->getSpare()->getName(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                ];
            }
        }

        return $result;
    }

    /**
     * Создает или возвращает изменяемую бронь
     *
     * @param array $params
     * @return Order|null
     */
    public function prepareOrder($params): ?Order
    {
        $orderId = (int)$params['orderId'];
        if (! empty($orderId)) {
            $order = $this->getRepository()->findByIdWithPlanning($orderId);
            if (empty($order)) {
                return null;
            }
        } else {
            $order = new Order();

            $status = $this->getStatusByAlias(OrderStatus::ALIAS_NEW);
            $order->setStatus($status);

            $paymentStatus = $this->getPaymentStatusByAlias(OrderPaymentStatus::NOT_PAYMENT);
            $order->setPaymentStatus($paymentStatus);
        }

        $expectedDate = $params['expectedDate'];
        $expectedDate = empty($expectedDate) ? date('Y-m-d') : $expectedDate;
        $order->setSeller($params['seller']);
        $order->setExpectedDate($expectedDate);
        $order->setDocument($params['documentName']);

        return $order;
    }

    /**
     * Возвращает из полученного заказа данные id планирований
     *
     * @param Order $order
     * @return array
     */
    public function getPlanningIdFromOrder($order) : array
    {
        if (empty($order->getId())) {
            return [];
        }

        $idPlanningForUpdate = [];
        foreach ($order->getItems() as $orderItems) {
            $planningItem = $orderItems->getPlanningItem();
            if (empty($planningItem)) {
                return [];
            }
            $idPlanningForUpdate[] = $planningItem->getPlanning()->getId();
        }
        return $idPlanningForUpdate;
    }
}
