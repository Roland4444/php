<?php

namespace Modules\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class TransportIncasRepository extends AbstractRepository
{
    public function getByPeriod(string $dateForm, string $dateTo): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->getClassName(), 'r')
            ->where('r.date >= :dateFrom and r.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateForm,
                'dateTo' => $dateTo,
            ]);
        return $qb->getQuery()->getResult();
    }

    public function getMoneySum(string $dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(r.money)')
            ->from($this->getClassName(), 'r')
            ->where('r.date <= :dateTo')
            ->setParameter('dateTo', $dateTo);
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }
}
