<?php
namespace Spare\Service;

use Finance\Service\OtherExpenseService;
use Spare\Entity\Order;
use Spare\Facade\Spare;

class PaymentService
{
    private $expenseService;
    private $orderService;
    private $sellerService;
    private $spare;

    public function __construct(
        OtherExpenseService $expenseService,
        OrderService $orderService,
        Spare $spare,
        SellerService $sellerService
    ) {
        $this->expenseService = $expenseService;
        $this->orderService = $orderService;
        $this->sellerService = $sellerService;
        $this->spare = $spare;
    }

    public function getBy($dateFrom, $dateTo, $sellerId = null, $name = null)
    {
        if (empty($sellerId)) {
            $sellers = $this->sellerService->findAll();
        } else {
            $sellers = [$this->sellerService->find($sellerId)];
        }
        $inns = $this->sellerService->getInnsBySellers($sellers);

        $expenses = $this->expenseService->getByInn($dateFrom, $dateTo, $inns, $name, 'tech');
        if (empty($expenses)) {
            return [];
        }

        $result = [];
        foreach ($expenses as $expense) {/**@var \Finance\Entity\OtherExpense $expense*/
            $seller = $this->sellerService->getByInn($sellers, $expense->getInn());
            $result[] = [
                'id' => $expense->getId(),
                'money' => (float)$expense->getMoney(),
                'comment' => str_replace("'", '"', $expense->getComment()),
                'date' => $expense->getDate(),
                'inn' => $expense->getInn(),
                'seller' => $seller->getName(),
                'orderId' => $expense->getOrder() ? $expense->getOrder()->getId() : null,
            ];
        }
        return $result;
    }

    public function getCashPayments($dateFrom, $dateTo, $name = null)
    {
        $expenses = $this->spare->getExpensesByCategoryAlias($dateFrom, $dateTo, 'tech', $name);
        if (empty($expenses)) {
            return [];
        }

        $result = [];
        foreach ($expenses as $expense) {
            $result[] = [
                'id' => $expense['id'],
                'inn' => 1, //todo хардкод надо что то с этим потом сделать
                'money' => (float)$expense['money'],
                'comment' => str_replace("'", '"', $expense['comment']),
                'date' => $expense['date'],
                'orderId' => $expense['order'] ? $expense['order']['id'] : null,
                'seller' => $expense['order'] ? $expense['order']['seller'] : null,
            ];
        }
        return $result;
    }

    /**
     * Выполняет сохранение и удаление связи между платежом и заказо
     *
     * @param \Finance\Entity\OtherExpense $expense
     * @param Order $order
     * @param bool $remove
     * @return array|string
     */
    public function editBind(\Finance\Entity\OtherExpense $expense, Order $order, $remove = false)
    {
        if ($remove) {
            $order->getExpenses()->removeElement($expense);
        } else {
            $order->addExpenses($expense);
        }

        $this->orderService->save($order);
        $this->orderService->updatePaymentStatusOrderByIds($order->getId());

        return [
            'orders' => $this->orderService->getNotPaid(),
        ];
    }

    public function editCashBind(\OfficeCash\Entity\OtherExpense $expense, Order $order, $remove = false)
    {
        if ($remove) {
            $order->getCashExpenses()->removeElement($expense);
        } else {
            $order->addCashExpenses($expense);
        }

        $this->orderService->save($order);
        $this->orderService->updatePaymentCashStatusOrderByIds($order->getId());

        return [
            'orders' => $this->orderService->getNotPaid(1),
        ];
    }
}
