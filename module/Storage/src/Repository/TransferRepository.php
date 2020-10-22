<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;

/**
 * Class TransferRepository
 * @package Storage\Repository
 */
class TransferRepository extends AbstractRepository
{
    /**
     * Получить данные для индексной страницы
     * @param array $params
     * @return mixed
     */
    public function getTableListData(array $params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t, s, d')
            ->from($this->getClassName(), 't')
            ->join('t.source', 's')
            ->join('t.dest', 'd')
            ->join('t.metal', 'm')
            ->orderBy('t.date', 'desc')
            ->andWhere('t.date > = :startdate and t.date <= :enddate')
            ->setParameter('startdate', $params['startdate'])
            ->setParameter('enddate', $params['enddate']);

        if (! empty($params['source'])) {
            $qb->andWhere("t.source = :source")
                ->setParameter('source', $params['source']);
        }
        if (! empty($params['dest'])) {
            $qb->andWhere("t.dest = :dest")
                ->setParameter('dest', $params['dest']);
        }
        if (! empty($params['department'])) {
            $qb->andWhere("s.id = :department or d.id = :department")
                ->setParameter('department', $params['department']);
        }
        if (! empty($params['metal'])) {
            $qb->andWhere("m.id = :metal")
                ->setParameter('metal', $params['metal']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getBalance($startDate, $endDate, $departmentId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<QUERY
            SELECT m.id,
                   m.name,
                   s1.weight as in_weight,
                   s2.weight as out_weight
            FROM metal m
            LEFT JOIN
              (SELECT m.id AS metal_id,
                      m.name,
                      sum(t.actual_weight) AS weight
               FROM metal m
               LEFT JOIN transfer t ON m.id=t.metal_id
               WHERE t.date>='$startDate'
                 AND date<='$endDate'
                 AND dest_department_id=$departmentId
               GROUP BY(m.id)) s1 ON m.id=s1.metal_id
            LEFT JOIN
              (SELECT m.id AS metal_id,
                      m.name,
                      sum(t.actual_weight) AS weight
               FROM metal m
               LEFT JOIN transfer t ON m.id=t.metal_id
               WHERE t.date>='$startDate'
                 AND date<='$endDate'
                 AND source_department_id=$departmentId
               GROUP BY(m.id)) s2 ON m.id=s2.metal_id
QUERY;
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }
}
