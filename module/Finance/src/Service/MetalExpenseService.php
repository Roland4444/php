<?php

namespace Finance\Service;

use Core\Service\AbstractService;
use Finance\Entity\MetalExpense;
use Finance\Entity\Order;
use Finance\Repositories\MetalExpenseRepository;
use Zend\I18n\View\Helper\CurrencyFormat;

class MetalExpenseService extends AbstractService
{
    protected function getRepository(): MetalExpenseRepository
    {
        return parent::getRepository();
    }

    public function getSumByDateGroupByBank(string $dateTo): array
    {
        return $this->getRepository()->getSumByDateGroupByBank($dateTo);
    }

    public function findByFilter(string $dateFrom, string $dateTo, ?int $customerId, ?int $bankId)
    {
        $data = $this->getRepository()->findByFilter($dateFrom, $dateTo, $customerId, $bankId);
        $sum = 0;
        /** @var MetalExpense $expense */
        foreach ($data as $expense) {
            $sum += $expense->getMoney();
        }
        $currencyFormatter = new CurrencyFormat();
        return [
            'data' => $data,
            'sum' => $currencyFormatter($sum, 'RUR', null, 'ru_RU'),
        ];
    }

    public function saveFromOrder(Order $order, array $params): void
    {
        $metalExpense = new MetalExpense();
        $metalExpense->setDate($order->getDate());
        $metalExpense->setBank($order->getSource());
        $metalExpense->setCustomer($params['customer']);
        $metalExpense->setPaymentNumber($order->getNumber());
        $metalExpense->setMoney($order->getMoney());
        $this->save($metalExpense);
    }

    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        if ($row->getId() > 0 || ! $this->hasDuplicate($row)) {
            $row->setPaymentNumber(trim($row->getPaymentNumber()));
            parent::save($row);
        }
    }

    private function hasDuplicate(MetalExpense $expense): bool
    {
        if ($expense->getPaymentNumber()) {
            $num = trim($expense->getPaymentNumber());
            $existsExpenses = $this->getRepository()->findBy([
                'payment_number' => $num,
                'date' => $expense->getDate()
            ]);
            return count($existsExpenses) > 0;
        }
        return false;
    }
}
