<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Reference\Entity\Trader;
use Reference\Service\TraderService;

class ReceiptFactoringTemplate implements Template
{
    /** @var Order */
    private $order;
    private $traderService;

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
        $factoringInn = '7802754982';
        $type = $order->getType();
        $source = $order->getSource();
        $dest = $order->getDest();
        return in_array($type, [1, 4]) && empty($source) && ! empty($dest) && trim($order->getSourceInn()) == $factoringInn;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return 'Факторинг поступления ' . $this->order->getMoney();
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        $traders = $this->traderService->findAll();
        /** @var Trader $trader */
        foreach ($traders as $trader) {
            if (mb_strpos($this->order->getComment(), trim($trader->getName()))) {
                return [
                    'trader' => $trader
                ];
            }
        }
        return [];
    }
}
