<?php

namespace Factoring\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class AssignmentDebtRepository extends AbstractRepository
{
    public function findByPeriod($dateStart, $dateEnd)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from($this->getClassName(), 'p')
            ->where('p.date >= :startdate and p.date <= :enddate')
            ->setParameter('startdate', $dateStart)
            ->setParameter('enddate', $dateEnd)
            ->orderBy('p.date', 'DESC');
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

    public function getSumGroupByTrader($dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('t.id, sum(p.money) as sum')
            ->from($this->getClassName(), 'p')
            ->where('p.date <= :dateTo')
            ->setParameter('dateTo', $dateTo)
            ->join('p.trader', 't')
            ->groupBy('p.trader')
            ->orderBy('p.date', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
