<?php

namespace Storage\Presenter;

use Storage\Entity\TransferList;
use Zend\I18n\View\Helper\NumberFormat;

class TransferPresenter
{
    private $items;
    private $avgSor;

    public function __construct(TransferList $itemList, $avgSor)
    {
        $this->items = $itemList;
        $this->avgSor = $avgSor;
    }

    public function getFormatData()
    {
        $items = array_map(function ($item) {
            return $this->presentItem($item);
        }, $this->items->toArray());

        return [
            'items' => $items,
            'sent' => $this->formatNumber($this->items->getAmountSent()). '&nbsp;кг.',
            'received' => $this->formatNumber($this->items->getAmountReceived()). '&nbsp;кг.',
            'total' => $this->formatTotal($this->items->getTotalGroupByMetal()),
            'avgSor' => $this->formatNumber($this->avgSor),
        ];
    }

    private function presentItem($item)
    {
        return [
            'id' => $item->getId(),
            'date' => $item->getDate()->format('d.m.Y'),
            'source' => $item->getSource()->getName(),
            'destination' => $item->getDest()->getName(),
            'destinationId' => $item->getDest()->getId(),
            'metal' => $item->getMetal()->getName(),
            'sent' => $this->formatNumber($item->getWeight()). '&nbsp;кг.',
            'received' => $this->formatNumber($item->getActual()). '&nbsp;кг.',
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

    private function formatTotal($totalData)
    {
        $result = [];
        foreach ($totalData as $item) {
            $result[] = [
                'title' => $item['title'],
                'sent' => $this->formatNumber($item['sent']). '&nbsp;кг.',
                'received' => $this->formatNumber($item['received']). '&nbsp;кг.',
            ];
        }
        return $result;
    }
}
