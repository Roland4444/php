<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Reference\Service\TraderService;

class ReceiptTraderTemplate implements Template
{
    private TraderService $traderService;
    private Order $order;
    private array $params = [];

    public function __construct(TraderService $traderService)
    {
        $this->traderService = $traderService;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        $type = $order->getType();
        $source = $order->getSource();
        $dest = $order->getDest();
        $trader = $this->traderService->findByInn($this->order->getSourceInn());
        if ($trader) {
            $this->params['trader'] = $trader;
        }
        return in_array($type, [1, 4]) && empty($source) && ! empty($dest) && $trader;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return 'Поступление от трейдера ' . $this->order->getMoney();
    }
}
