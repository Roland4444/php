<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Core\Utils\Conditions;
use Core\Utils\Options;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Reference\Entity\Customer;
use Reference\Entity\Metal;

class PurchaseRepository extends AbstractRepository
{
    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return mixed|object|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r,c,m')
            ->from($this->getClassName(), 'r')
            ->join('r.customer', 'c')
            ->join('r.metal', 'm')
            ->where('r.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getSingleResult();
    }

    public function getTableListData(array $params, string $storageType)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p, c, m')
            ->from($this->getClassName(), 'p')
            ->join('p.customer', 'c')
            ->join('p.metal', 'm')
            ->join('p.department', 'd')
            ->leftJoin('p.deal', 'dl')
            ->where('p.date > = :startdate and p.date <= :enddate')
            ->setParameter('startdate', $params['startdate'])
            ->setParameter('enddate', $params['enddate'])
            ->addOrderBy('p.date', 'DESC')
            ->addOrderBy('dl.id', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('m.name', 'ASC');
        if ($params['visibleOnly']) {
            $qb->andWhere(Conditions::jsonNotContains('d', Options::HIDE));
        }
        if (! empty($params['customer'])) {
            $qb->andWhere('c.id = :customer')
                ->setParameter('customer', $params['customer']);
        }
        if (! empty($params['qr'])) {
            $qb->andWhere('dl.code = :qr')
                ->setParameter('qr', $params['qr']);
        }
        if ($params['department']) {
            $qb->andWhere('d.id = :department')
                ->setParameter('department', $params['department']);
        } else {
            $qb->andWhere('d.type = :type')
                ->setParameter('type', $storageType);
        }

        return $qb->getQuery()->getResult();
    }

    public function getTotal(array $params, string $storageType)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m.name as metal, sum(p.weight) as weight, (sum(p.weight*p.cost)/sum(p.weight)) as price')
            ->from($this->getClassName(), 'p')
            ->join('p.customer', 'c')
            ->join('p.metal', 'm')
            ->join('p.department', 'd')
            ->leftJoin('p.deal', 'dl')
            ->groupBy('m.name')
            ->where('p.cost > 0')
            ->andWhere('p.date > = :startdate and p.date <= :enddate')
            ->setParameter('startdate', $params['startdate'])
            ->setParameter('enddate', $params['enddate']);
        if ($params['visibleOnly']) {
            $qb->andWhere(Conditions::jsonNotContains('d', Options::HIDE));
        }
        if (! empty($params['customer'])) {
            $qb->andWhere('c.id = :customer')
                ->setParameter('customer', $params['customer']);
        }
        if (! empty($params['qr'])) {
            $qb->andWhere('dl.code = :qr')
                ->setParameter('qr', $params['qr']);
        }
        if ($params['department']) {
            $qb->andWhere('d.id = :department')
                ->setParameter('department', $params['department']);
        } else {
            $qb->andWhere('d.type = :type')
                ->setParameter('type', $storageType);
        }
        return $qb->getQuery()->getResult();
    }

    public function getByDeal(int $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->getClassName(), 'p')
            ->join('p.customer', 'c')
            ->join('p.metal', 'm')
            ->join('p.department', 'd')
            ->leftJoin('p.deal', 'dl')
            ->where('dl.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

    public function getWeightByMetal(string $dateFrom, string $dateTo, ?int $departmentId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('IDENTITY(p.metal) as id, sum(p.weight) as sum')
            ->from($this->getClassName(), 'p')
            ->andWhere('p.date >= :dateFrom and p.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,

            ])->groupBy('p.metal');
        if ($departmentId) {
            $qb->andWhere('p.department = :departmentId')->setParameter('departmentId', $departmentId)
                ->setParameter('departmentId', $departmentId);
        }
        return $qb->getQuery()->getResult();
    }

    public function getGroupPurchaseByDay(string $date, int $departmentId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('m.name as metal, sum(p.weight) as weight')
            ->from($this->getClassName(), 'p')
            ->join('p.metal', 'm')
            ->where('p.date = :date')
            ->andWhere('p.department = :department')
            ->groupBy('m.name')
            ->setParameter('date', $date)
            ->setParameter('department', $departmentId);
        return $qb->getQuery()->getResult();
    }

    public function getTotalByGroup(string $dateFrom, string $dateTo)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('g.name as metal, sum(p.weight) as weight, (sum(p.weight*p.cost)/sum(p.weight)) as price')
            ->from($this->getClassName(), 'p')
            ->join('p.metal', 'm')
            ->join('m.group', 'g')
            ->groupBy('g.name')
            ->where('p.cost > 0')
            ->andWhere('p.date > = :dateFrom and p.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
        return $qb->getQuery()->getResult();
    }

    public function findByDateAndDepartment(string $date, int $departmentId)
    {
        return $this->getEntityManager()->getRepository($this->getClassName())
            ->findBy([
                'date' => $date,
                'department' => $departmentId
            ], ['date' => 'ASC']);
    }

    public function getMetalReference(int $id)
    {
        $metalRepo = $this->getEntityManager()->getRepository(Metal::class);
        return $metalRepo->getReference($id);
    }

    public function getCustomerReference(int $id)
    {
        $customerRepo = $this->getEntityManager()->getRepository(Customer::class);
        return $customerRepo->getReference($id);
    }
}
