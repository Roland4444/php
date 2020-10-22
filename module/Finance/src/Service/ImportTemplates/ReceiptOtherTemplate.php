<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;

class ReceiptOtherTemplate implements Template
{
    private $order;

    /**
     * {@inheritDoc}
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        $type = $order->getType();
        $source = $order->getSource();
        $dest = $order->getDest();
        return in_array($type, [1, 4]) && empty($source) && ! empty($dest);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return 'Прочие поступления - ' . $this->order->getMoney();
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [];
    }
}
