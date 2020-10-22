<?php

namespace Core\Traits;

use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\I18n\View\Helper\NumberFormat;

trait FormatNumbers
{
    /**
     * @param $number
     * @return string
     */
    protected function formatNumber($number): string
    {
        $numberFormat = new NumberFormat();
        return $numberFormat->__invoke(
            $number,
            \NumberFormatter::DECIMAL,
            \NumberFormatter::TYPE_DEFAULT,
            'ru_RU'
        );
    }

    public function formatCurrency($number): string
    {
        $numberFormat = new CurrencyFormat();
        return $numberFormat($number, 'RUR', null, 'ru_RU');
    }
}
