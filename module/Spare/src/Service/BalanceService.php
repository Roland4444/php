<?php


namespace Spare\Service;

use Spare\Dao\BalanceDao;
use Zend\I18n\View\Helper\CurrencyFormat;

class BalanceService
{
    private $dao;

    public function __construct(BalanceDao $dao)
    {
        $this->dao = $dao;
    }

    public function getTotal($dateEnd)
    {
        $total = $this->dao->getTotal($dateEnd);
        $result = [];
        $currencyFormatter = new CurrencyFormat();
        foreach ($total as $item) {
            $expense_sum = $item['expense_sum'] + $item['cash_expense_sum'];
            $balance = $item['order_sum'] - $expense_sum + $item['return_sum'];
            if ($balance != 0) {
                $item['balance'] = $currencyFormatter($balance, 'RUR');
                $item['order_sum'] = $currencyFormatter($item['order_sum'], 'RUR');
                $item['expense_sum'] = $currencyFormatter($expense_sum, 'RUR');
                $result[] = $item;
            }
        }
        return $result;
    }
}
