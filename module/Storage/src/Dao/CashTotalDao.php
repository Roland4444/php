<?php

namespace Storage\Dao;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class CashTotalDao
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $departmentId
     * @param $dateEnd
     * @return mixed[]
     * @throws DBALException
     */
    public function getTotalByDepartment(int $departmentId, $dateEnd)
    {
        $conn = $this->entityManager->getConnection();
        $sql = <<<QUERY
            SELECT s1.customer_id, s1.customer, s1.legal, s1.inspection_date, s1.purchase, s1.formal_sum, s2.payment, s3.payment as main_payment FROM 
                (
                    SELECT c.id AS customer_id, c.legal, c.inspection_date, c.name AS customer, 
                        sum(
                            CASE 
                                WHEN p.formal_cost=0 OR p.formal_cost IS NULL 
                                THEN p.weight * p.cost 
                                ELSE p.weight * p.formal_cost 
                            END
                        ) AS formal_sum, 
                        sum(p.weight * p.cost) AS purchase 
                    FROM purchase p RIGHT JOIN customer c 
                        ON p.customer_id=c.id AND p.department_id=$departmentId AND p.date <='$dateEnd' 
                    GROUP BY c.name
                ) s1 
            LEFT JOIN 
                (
                    SELECT c.name AS customer, sum(e.money) AS payment FROM storage_metal_expense e 
                    RIGHT JOIN customer c 
                    ON e.customer_id=c.id AND e.department_id=$departmentId AND e.formal=0 AND e.date <='$dateEnd'
                    GROUP BY c.name
                ) s2 ON s1.customer=s2.customer 
            LEFT JOIN 
                (
                    SELECT c.name AS customer, sum(e.money) AS payment
                    FROM main_metal_expenses e RIGHT JOIN customer c 
                    ON e.customer_id=c.id AND c.legal=1 AND e.date <='$dateEnd'
                    GROUP BY c.name
                ) s3 on s1.customer=s3.customer 
            ORDER BY s1.customer
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }

    /**
     * @param $dateEnd
     * @return mixed[]
     * @throws DBALException
     */
    public function getTotal($dateEnd)
    {
        $conn = $this->entityManager->getConnection();
        $sql = <<<QUERY
            SELECT s1.customer_id, s1.inspection_date, s1.legal, s1.customer, s1.purchase, s1.purchase_formal, 
            s3.payment as payment, s4.payment as main_payment, s2.payment_formal, (s1.purchase_formal - s2.payment_formal) AS formal_sum, 
            (s1.purchase - s3.payment) AS fact_sum FROM 
            (
                SELECT c.id as customer_id, c.inspection_date, c.legal, c.name AS customer, 
                    sum(CASE 
                        WHEN p.formal_cost=0 OR p.formal_cost IS NULL 
                        THEN p.weight * p.cost 
                        ELSE p.weight * p.formal_cost 
                    END) as purchase_formal, 
                    sum(p.weight * p.cost) AS purchase 
                FROM purchase p RIGHT JOIN customer c ON p.customer_id=c.id AND c.legal=1
                WHERE p.date <='$dateEnd'
                GROUP BY c.name
            ) s1 
        LEFT JOIN 
            (
                SELECT c.name AS customer, sum(e.money) AS payment_formal 
                FROM storage_metal_expense e RIGHT JOIN customer c ON e.customer_id=c.id AND c.legal=1
                WHERE e.formal=1 AND e.date <='$dateEnd'
                GROUP BY c.name
            ) s2 on s1.customer=s2.customer 
        LEFT JOIN 
            (
                SELECT c.name AS customer, sum(e.money) AS payment
                FROM storage_metal_expense e RIGHT JOIN customer c 
                ON e.customer_id=c.id AND c.legal=1 AND e.date <='$dateEnd'
                GROUP BY c.name
            ) s3 on s1.customer=s3.customer 
        LEFT JOIN 
            (
                SELECT c.name AS customer, sum(e.money) AS payment
                FROM main_metal_expenses e RIGHT JOIN customer c 
                ON e.customer_id=c.id AND c.legal=1 AND e.date <='$dateEnd'
                GROUP BY c.name
            ) s4 on s1.customer=s4.customer 
        ORDER BY s1.customer 
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }
}
