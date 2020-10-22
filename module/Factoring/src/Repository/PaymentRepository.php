<?php

namespace Factoring\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class PaymentRepository extends AbstractRepository
{
    public function findByPeriod($dateStart, $dateEnd, $traderId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')->from($this->getClassName(), 'p')
            ->where('p.date >= :startdate and p.date <= :enddate')
            ->setParameter('startdate', $dateStart)
            ->setParameter('enddate', $dateEnd)
            ->orderBy('p.date', 'DESC');
        if (! empty($traderId)) {
            $qb->andWhere('p.trader = :traderId')
                ->setParameter('traderId', $traderId);
        }
        return $qb->getQuery()->getResult();
    }

    public function getSumFactoring($dateEnd, $bankId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->getClassName(), 'p')
            ->where('p.date <= :enddate')
            ->setParameter('enddate', $dateEnd);
        if ($bankId) {
            $qb->andWhere('p.bank = :bankId')
                ->setParameter('bankId', $bankId);
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSumByPeriodGroupByBank(string $dateTo): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<QUERY
            SELECT b.id,
                   s1.sum
            FROM bank_account b
            LEFT JOIN
              (SELECT r.bank_id AS bankId, sum(r.money) AS sum
               FROM factoring_payments r
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

    public function getSumGroupByTrader($dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('t.id, sum(p.money) as sum')
            ->from($this->getClassName(), 'p')
            ->andWhere('p.date <= :dateTo')
            ->setParameter('dateTo', $dateTo)
            ->join('p.trader', 't')
            ->groupBy('p.trader')
            ->orderBy('p.date', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
