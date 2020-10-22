<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Storage\Repository\Interfaces\WeighingRepositoryInterface;

class WeighingRepository extends AbstractRepository implements WeighingRepositoryInterface
{
    /**
     * Получить все взвешивания отфильтрованные
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getClassName(), 'r');

        if (! empty($params['department'])) {
            $qb->where('r.department = :department')
                ->setParameter('department', $params['department']);
        }

        if (! empty($params['startdate'])) {
            $qb->andWhere(" r.date >= '{$params['startdate']}' ");
        } else {
            $qb->andWhere(" r.date >= '" . date('Y-m-01') . "' ");
        }

        if (! empty($params['enddate'])) {
            $qb->andWhere(" r.date <= '{$params['enddate']}' ");
        } else {
            $qb->andWhere(" r.date <= '" . date('Y-m-t') . "' ");
        }

        $qb->orderBy('r.date', 'desc');
        $qb->orderBy('r.waybill', 'desc');

        return $qb->getQuery()->getResult();
    }

    public function getWeighingByExportIdDepartmentDate($data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getClassName(), 'r')
            ->andWhere('r.date = :date')
            ->andWhere('r.exportId = :exportId')
            ->andWhere('r.department = :department')
            ->setParameter('date', $data->date)
            ->setParameter('exportId', $data->id)
            ->setParameter('department', $data->departmentId);

        return $qb->getQuery()->getResult();
    }

    public function getByDateAndDepartment(string $date, int $departmentId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getClassName(), 'r')
            ->join('r.weighingItems', 'i')
            ->leftJoin('r.customer', 'c')
            ->join('i.metal', 'm')
            ->join('r.department', 'd')
            ->andWhere('r.department = :department')
            ->andWhere('r.date = :date')
            ->setParameter('department', $departmentId)
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }
}
