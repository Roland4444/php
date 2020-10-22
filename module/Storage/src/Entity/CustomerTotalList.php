<?php

namespace Storage\Entity;

class CustomerTotalList implements \IteratorAggregate
{
    private array $items = [];

    public function addItem(CustomerTotal $customerTotal): void
    {
        if (round($customerTotal->getFactBalance()) != 0) {
            $this->items[] = $customerTotal;
        }
    }

    public function getBalance(): float
    {
        $result = 0;
        foreach ($this->items as $item) { /** @var CustomerTotal $item */
            $result += $item->getFactBalance();
        }
        return $result;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
