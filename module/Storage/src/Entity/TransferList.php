<?php

namespace Storage\Entity;

class TransferList implements \IteratorAggregate
{
    /**
     * @var Transfer[]
     */
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function getAmountSent()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getWeight();
        }
        return $sum;
    }

    public function getAmountReceived()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getActual();
        }
        return $sum;
    }

    public function getTotalGroupByMetal()
    {
        $result = [];
        foreach ($this->items as $item) {
            $metalId = $item->getMetal()->getId();
            if (empty($result[$metalId])) {
                $result[$metalId] = [
                    'title' => $item->getMetal()->getName(),
                    'sent' => $item->getWeight(),
                    'received' => $item->getActual(),
                ];
            } else {
                $result[$metalId]['sent'] += $item->getWeight();
                $result[$metalId]['received'] += $item->getActual();
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function toArray()
    {
        return $this->items;
    }
}
