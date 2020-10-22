<?php

namespace Finance\Repositories;

use Core\Repository\AbstractRepository;
use Finance\Entity\MetalExpense;

class MetalExpenseRepository extends AbstractRepository
{
    public function getSumByDateGroupByBank(string $dateTo): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<QUERY
            SELECT b.id,
                   s1.sum
            FROM bank_account b
            LEFT JOIN
              (SELECT r.bank_id AS bankId, sum(r.money) AS sum
               FROM main_metal_expenses r
               WHERE date<='{$dateTo}'
               GROUP BY r.bank_id) s1 ON b.id=s1.bankId
            WHERE b.closed=FALSE
QUERY;
        $rows = $conn->query($sql);
        $result = [];
        foreach ($rows->fetchAll() as $row) {
            $result[$row['id']] = $row['sum'] ? $row['sum'] : 0;
        }
        return $result;
    }

    public function findByFilter(string $dateFrom, string $dateTo, ?int $customerId, ?int $bankId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m, b')
            ->from(MetalExpense::class, 'm')
            ->join('m.bank', 'b')
            ->orderBy('m.date', 'desc');
        if (! empty($dateFrom) && ! empty($dateTo)) {
            $qb->andWhere("m.date >= :date_from and m.date <= :date_to")
                ->setParameters([
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]);
        }
        if (! empty($customerId)) {
            $qb->andWhere('m.customer = :customer')
                ->setParameter('customer', $customerId);
        }
        if (! empty($bankId)) {
            $qb->andWhere('b.id = :account')
                ->setParameter('account', $bankId);
        }
        return $qb->getQuery()->getResult();
    }
}
