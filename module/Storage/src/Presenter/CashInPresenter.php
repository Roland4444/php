<?php

namespace Storage\Presenter;

use Zend\I18n\View\Helper\NumberFormat;

class CashInPresenter
{
    private $items;

    public function __construct($itemList)
    {
        $this->items = $itemList;
    }

    public function getFormatData()
    {
        $result = [];
        $sum = 0;
        foreach ($this->items as $item) {
            $result[] = $this->presentItem($item);
            $sum += $item->getMoney();
        }
        return [
            'items' => $result,
            'sum' => $this->formatNumber($sum) . '&nbsp;р.',
        ];
    }

    private function presentItem($item)
    {
        return [
            'id' => $item->getId(),
            'date' => (new \DateTime($item->getDate()))->format('d.m.Y'),
            'amount' => $this->formatNumber($item->getMoney()) . '&nbsp;р.',
        ];
    }

    /**
     * @param $weight
     * @return string
     */
    private function formatNumber($weight)
    {
        //TODO убрать зависимость на zend
        $numberFormat = new NumberFormat();
        return $numberFormat->__invoke(
            $weight,
            \NumberFormatter::DECIMAL,
            \NumberFormatter::TYPE_DEFAULT,
            "ru_RU"
        );
    }
}
