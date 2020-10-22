<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;

interface Template
{
    /**
     * @param Order $order
     * @return boolean
     */
    public function equals(Order $order);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return array
     */
    public function getParams(): array;
}
