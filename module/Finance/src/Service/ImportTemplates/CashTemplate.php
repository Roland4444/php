<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Finance\Service\BankService;

class CashTemplate implements Template
{
    private $order;
    private $bankAccountCash;

    public function __construct(BankService $bankService)
    {
        $this->bankAccountCash = $bankService->findCash();
    }

    /**
     * {@inheritDoc}
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        if (in_array($order->getType(), [3, 9])) {
            return true;
        } elseif ($order->getType() == 13 && $order->getSource()) {
            if (strpos(strtolower($order->getComment()), 'Списание средств') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return 'Приход в наличные - ' . $this->order->getMoney();
    }

    public function getParams(): array
    {
        return ['bank' => $this->bankAccountCash];
    }
}
