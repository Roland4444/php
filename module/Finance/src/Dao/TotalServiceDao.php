<?php

namespace Finance\Dao;

use Core\Utils\Conditions;
use Core\Utils\Options;
use Finance\Entity\OtherReceipts;

class TotalServiceDao
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTotalByTraders($endDate)
    {
        $conn = $this->entityManager->getConnection();
        $hide = Options::HIDE;
        $notFactoring = Conditions::jsonNotContains('s', 'factoring');
        $sql = <<<QUERY
 SELECT trader.id, trader.parent_id, trader.name, 
        parent.ord, parent.name AS parent_name, JSON_CONTAINS(parent.options , '["$hide"]') AS $hide,
        q3.sh_sum AS dol_sum, q1.sh_sum AS rub_sum, p_sum 
    FROM trader 
    JOIN trader_parent parent ON parent.id=trader.parent_id 
        LEFT JOIN 
            -- subquery get sum shipment to trader in rubles
            (SELECT trader.id AS id, SUM(ci.real_weight * ci.cost)/1000 AS sh_sum FROM `shipment` s 
                JOIN containers c ON s.id=c.shipment_id 
                    JOIN container_items ci on c.id=ci.container_id 
                        JOIN trader ON s.trader_id=trader.id WHERE cost_dol=0 AND $notFactoring AND date <='$endDate' GROUP BY s.trader_id) q1 ON trader.id=q1.id 
        LEFT JOIN 
            -- subquery get sum trader receipts group by trader
            (SELECT trader.id as id, SUM(tr.money) as p_sum FROM `main_receipts_trader` tr 
                JOIN trader ON trader.id=tr.trader_id WHERE date <='$endDate' GROUP BY trader.id) q2 ON trader.id=q2.id 
        LEFT JOIN
            -- subquery get sum shipment to trader in dollars 
            (SELECT trader.id AS id, SUM(ci.real_weight * ci.cost_dol*s.dollar_rate)/1000 AS sh_sum FROM `shipment` s 
                JOIN containers c ON s.id=c.shipment_id JOIN container_items ci ON c.id=ci.container_id 
                    JOIN trader ON s.trader_id=trader.id WHERE cost_dol!=0 AND $notFactoring AND date <='$endDate' GROUP BY s.trader_id) q3 ON trader.id=q3.id
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }

    public function getOverdraft(string $dateTo)
    {
        $conn = $this->entityManager->getConnection();
        $overdraft = Options::OVERDRAFT;
        $sql = <<<QUERY
    SELECT bank.id, ( 
        SELECT SUM(r.money) 
            FROM main_receipts as r 
                WHERE 
                       r.bank_account_id = bank.id 
                       and JSON_CONTAINS(r.options , '["{$overdraft}"]')
                       AND r.date <= '{$dateTo}'
        ) as receipts, (
        SELECT SUM(e.money) 
            FROM main_other_expenses as e
            JOIN cost_category c ON e.category_id = c.id
                WHERE 
                       e.bank_id = bank.id
                     AND c.alias = 'overdraft'
                     AND e.date <= '{$dateTo}'
        ) as expenses
    FROM bank_account as bank HAVING (receipts IS NOT null) OR (expenses IS NOT null)
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }
}
