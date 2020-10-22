<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;

class WeighingItemRepository extends AbstractRepository
{
    public function getGrouppedByMetal(string $dateFrom, string $dateTo, int $departmentId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $massQuery = 'sum((w.brutto - w.tare - w.trash) * ((100 - w.clogging) / 100))';

        $qb->select("
                $massQuery as mass, m.name, ($massQuery * w.price) / $massQuery as avg_price
            ")
            ->from($this->getClassName(), 'w')
            ->join('w.weighing', 'r')
            ->join('w.metal', 'm');

        if (! empty($departmentId)) {
            $qb->where('r.department = :department')
                ->setParameter('department', $departmentId);
        }

        if (! empty($dateFrom)) {
            $qb->andWhere("r.date >= '$dateFrom'");
        } else {
            $qb->andWhere(" r.date >= '" . date('Y-m-01') . "' ");
        }

        if (! empty($dateTo)) {
            $qb->andWhere("r.date <= '$dateTo'");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-t') . "' ");
        }

        $qb->groupBy('m.id');

        return $qb->getQuery()->getResult();
    }
}
