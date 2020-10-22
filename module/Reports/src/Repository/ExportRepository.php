<?php

namespace Reports\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Reference\Entity\Department;
use Reports\Entity\RemoteSklad;

class ExportRepository extends EntityRepository
{
    const TYPE_EXPORT = 1;
    const TYPE_TRANSFER = 2;

    /**
     * Получить средний засор
     * @param array $params
     * @return mixed
     */
    public function getAvgSor(array $params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        //$qb->select('(SUM(r.massa * 100 / (100 - r.sor)) - SUM(r.massa)) * 100 / (SUM(r.massa * 100 / (100 - r.sor)))')
        $qb->select('sum(r.sor)/count(r.id)')
            ->from(RemoteSklad::class, 'r')
            ->andWhere("r.date >= :startdate")
            ->andWhere("r.date <= :enddate");

        if (! empty($params['startdate'])) {
            $qb->setParameter('startdate', $params['startdate']);
        } else {
            $qb->setParameter('startdate', date('Y-m-d'));
        }

        if (! empty($params['enddate'])) {
            $qb->setParameter('enddate', $params['enddate']);
        } else {
            $qb->setParameter('enddate', date('Y-m-d'));
        }

        if (! empty($params['department'])) {
            $department = $this->getEntityManager()->getRepository(Department::class)->find($params['department']);
            $qb->andWhere("r.sklad LIKE '%{$department->getName()}%'");
        }

        if ($params['type'] == self::TYPE_EXPORT || empty($params['type'])) {
            // Экспорт
            $qb->andWhere("r.massa > 0 and r.transfer is null");
        } elseif ($params['type'] == self::TYPE_TRANSFER) {
            // Переброска
            $qb->andWhere("r.massa < 0 or r.transfer is not null");
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getMassArray($dateFrom, $dateTo, $departmentName)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r.gruz as metal, sum(r.massa) as weight')
            ->from(RemoteSklad::class, 'r')
            ->groupBy('r.gruz')
            ->where('r.date >= :dateFrom and r.date <= :dateTo')
            ->andWhere('r.sklad = :departmentName')
            ->andWhere("r.massa > 0 and r.transfer is null")
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->setParameter('departmentName', $departmentName);

        return $qb->getQuery()->getResult();
    }
}
