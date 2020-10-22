<?php


namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;
use Storage\Repository\Interfaces\MetalExpenseRepositoryInterface;

class MetalExpenseRepository extends AbstractRepository implements MetalExpenseRepositoryInterface
{

    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId, $customerId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->getClassName(), 'p')
            ->join('p.department', 'd')
            ->join('p.customer', 'c')
        ->where("p.date >='" . $dateFrom . "' and p.date<='" . $dateTo . "'");
        if ($departmentId) {
            $qb->andWhere("d.id = " . $departmentId);
        }
        if ($customerId) {
            $qb->andWhere("c.id = " . $customerId);
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSum($params = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(p.money)')
        ->from($this->getClassName(), 'p')
        ->join('p.department', 'd')
        ->join('p.customer', 'c')
        ->where('p.date >=\'' . $params['startdate'] . '\' and p.date<=\'' . $params['enddate'] . '\'');
        if (! empty($params['department'])) {
            $qb->andWhere("d.id = " . $params['department']);
        }
        if (! empty($params['customer'])) {
            $qb->andWhere("c.id = " . $params['customer']);
        }
        if (! empty($params['payment'])) {
            $qb->andWhere("p.diamond = " . intval($params['payment'] == 'diamond'))
            ->andWhere("p.formal = " . intval($params['payment'] == 'formal'));
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function findAll($params = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r,d,c,w')
            ->from($this->getClassName(), 'r')
            ->join('r.department', 'd')
            ->join('r.customer', 'c')
            ->leftJoin('r.weighing', 'w')
            ->addOrderBy('r.date', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('w.waybill', 'DESC');
        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $qb->andWhere("r.date >= '" . $params['startdate'] . "' and r.date<='" . $params['enddate'] . "'");
        }
        if (! empty($params['department'])) {
            $qb->andWhere("d.id = " . $params['department']);
        }
        if (! empty($params['customer'])) {
            $qb->andWhere("c.id = " . $params['customer']);
        }
        if (! empty($params['payment'])) {
            $qb->andWhere("r.diamond = " . intval($params['payment'] == 'diamond'))
            ->andWhere("r.formal = " . intval($params['payment'] == 'formal'));
        }

        return $qb->getQuery()->getResult();
    }

    public function getSumByDeal(int $dealId): ?float
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('sum(p.money)')
            ->from($this->getClassName(), 'p')
            ->where('p.deal = :dealId')
            ->setParameter('dealId', $dealId);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
        return $result ?? 0;
    }
}
