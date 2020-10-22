<?php

namespace Spare\Repositories;

use Core\Repository\AbstractRepository;
use Spare\Entity\PlanningStatus;

class PlanningRepository extends AbstractRepository
{
    /**
     * Производит запрос на поиск всех заявок на запчасти с установленными фильтрами
     *
     * @param array $params
     * @return mixed
     */
    public function getTableListData($params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p, s, i, sp, emp, v, o')
            ->from($this->getClassName(), 'p')
            ->join('p.status', 's')
            ->join('p.items', 'i')
            ->leftJoin('p.employee', 'emp')
            ->leftJoin('p.vehicle', 'v')
            ->leftJoin('i.itemsOrder', 'o')
            ->join('i.spare', 'sp')
            ->orderBy('p.date', 'desc')
            ->addOrderBy('p.id', 'desc');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $qb->andWhere("p.date >= :startdate and p.date <= :enddate")
                ->setParameters([
                    'startdate' => $params['startdate'],
                    'enddate' => $params['enddate'],
                ]);
        }

        if (! empty($params['planningStatus'])) {
            $qb->andWhere("p.status = :status")->setParameter('status', $params['planningStatus']);
        }

        if (! empty($params['notReturnClose'])) {
            $qb->andWhere("s.alias <> '" . PlanningStatus::ALIAS_CLOSED . "'"
                    . " AND s.alias <> '" . PlanningStatus::ALIAS_ORDERED . "'");
        }

        if (! empty($params['spare'])) {
            $qb->andWhere("i.spare = :spare")
                ->setParameter('spare', $params['spare']);
        }

        if (! empty($params['number'])) {
            $qb->andwhere("p.id = :planningId")
                ->setParameter('planningId', $params['number']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getByItemIds(array $ids)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from($this->getClassName(), 'p')
            ->join('p.items', 'i')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $ids);
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $ids
     * @param $status
     */
    public function updateStatus($ids, $status)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->update($this->getClassName(), 's')
            ->set('s.status', "'{$status}'")
            ->where("s.id IN ({$ids})");
        $queryBuilder->getQuery()->execute();
    }
}
