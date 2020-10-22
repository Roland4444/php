<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Core\Utils\Conditions;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Storage\Entity\ContainerItem;

class ShipmentRepository extends AbstractRepository
{
    public function findByParams(string $dateFrom, string $dateTo, ?int $departmentId, ?int $traderId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r,t,d')
            ->from($this->getClassName(), 'r')
            ->join('r.trader', 't')
            ->join('r.department', 'd')
            ->where('r.date >= :dateFrom and r.date <= :dateTo ')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ])
            ->orderBy('r.date', 'DESC');
        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }
        if ($traderId) {
            $qb->andWhere('t.id = ' . $traderId);
        }
        return $qb->getQuery()->getResult();
    }

    public function getDuplicate($post, $depId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r,t,d,tar')
            ->from($this->getClassName(), 'r')
            ->join('r.trader', 't')
            ->join('r.department', 'd')
            ->join('r.tariff', 'tar')
            ->where('r.date = :date')->setParameter('date', $post['date'])
            ->andWhere('t.id = :traderId')->setParameter('traderId', $post['trader'])
            ->andWhere('d.id = :departmentId')->setParameter('departmentId', $depId)
            ->andWhere('tar.id = :tariffId')->setParameter('tariffId', $post['tariff']);

        return $qb->getQuery()->getResult();
    }

    public function findWithFactoring(string $dateFrom, string $dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')
            ->from($this->getClassName(), 's')
            ->where('s.date >= :startdate and s.date <= :enddate')
            ->andWhere(Conditions::jsonContains('s', 'factoring'))
            ->setParameter('startdate', $dateFrom)
            ->setParameter('enddate', $dateTo);

        return $qb->getQuery()->getResult();
    }

    public function getSumFactoringRub($dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(i.realWeight * i.cost) / 1000')
            ->from($this->getClassName(), 's')
            ->join('s.containers', 'c')
            ->join('c.items', 'i')
            ->where('s.date <= :enddate')
            ->andWhere('i.costDol = 0')
            ->andWhere(Conditions::jsonContains('s', 'factoring'))
            ->setParameter('enddate', $dateTo);
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSumFactoringDol($dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(i.realWeight * i.cost) / 1000')
            ->from($this->getClassName(), 's')
            ->join('s.containers', 'c')
            ->join('c.items', 'i')
            ->where('s.date <= :enddate')
            ->andWhere('i.costDol != 0')
            ->andWhere(Conditions::jsonContains('s', 'factoring'))
            ->setParameter('enddate', $dateTo);
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }
}
