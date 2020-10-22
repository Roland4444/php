<?php

namespace Storage\Entity;

class Check
{
    private $purchases;
    private $sum;

    public function __construct(array $purchases)
    {
        $this->purchases = $purchases;

        $sum = 0;
        foreach ($purchases as $purchase) {
            $sum = bcadd($sum, $purchase->getSum(), 16);
        }
        $this->sum = $sum;
    }

    /**
     * @return array
     */
    public function getPurchases(): array
    {
        return $this->purchases;
    }

    public function getSum(): string
    {
        return $this->sum;
    }
}
