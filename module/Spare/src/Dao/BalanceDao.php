<?php

namespace Spare\Dao;

class BalanceDao
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTotal($dateEnd)
    {
        $conn = $this->entityManager->getConnection();
        $sql = <<<QUERY
             SELECT s1.id, s1.name, s1.order_sum, s2.expense_sum, s3.cash_expense_sum, s4.return_sum FROM 
                (
                    SELECT s.id, s.name, sum(i.quantity * i.price) as order_sum 
                    FROM spare_order_items i 
                    JOIN spare_order o ON i.order_id=o.id and o.date <= '$dateEnd'
                    RIGHT JOIN spare_seller s ON o.seller_id=s.id 
                    GROUP BY o.seller_id
                ) s1 
            JOIN 
                (
                    SELECT s.id, sum(oe.money) as expense_sum
                    FROM main_other_expenses oe 
                    JOIN spare_order_expense_ref er ON oe.id=er.expense_id and oe.date <= '$dateEnd' 
                    RIGHT JOIN spare_order o ON o.id=er.order_id 
                    JOIN spare_seller s ON o.seller_id=s.id  
                    GROUP BY s.id
                ) s2 ON s1.id=s2.id
            JOIN 
                (
                    SELECT s.id, sum(oe.money) as cash_expense_sum
                    FROM storage_other_expense oe 
                    JOIN spare_order_cash_expense_ref er ON oe.id=er.expense_id and oe.date <= '$dateEnd' 
                    RIGHT JOIN spare_order o ON o.id=er.order_id 
                    JOIN spare_seller s ON o.seller_id=s.id  
                    GROUP BY s.id
                ) s3 ON s1.id=s3.id
            JOIN 
                (
                    SELECT s.id, sum(r.money) as return_sum
                    FROM spare_seller_returns r 
                    RIGHT JOIN spare_seller s ON r.seller_id=s.id and r.date <= '$dateEnd' 
                    GROUP BY s.id
                ) s4 ON s1.id=s4.id
            ORDER BY s1.name;
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }
}
