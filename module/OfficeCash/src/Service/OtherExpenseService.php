<?php
namespace OfficeCash\Service;

use Application\Service\BaseService;
use Doctrine\ORM\Query;

class OtherExpenseService extends BaseService
{
    protected $entity = '\OfficeCash\Entity\OtherExpense';
    protected $order = [ 'date' => 'ASC'];
    protected $startdate;
    protected $enddate;
    protected $department;
    protected $category;
    protected $comment;

    public function setDates($startdate, $enddate)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function setDepartment($dep)
    {
        $this->department = $dep;
    }

    /**
     * Get total sum by department
     * @param $dateFrom
     * @param $dateTo
     * @param $departmentId
     */
    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->join('p.department', 'd')
            ->where("p.date >='".$dateFrom."' and p.date<='".$dateTo."'");
        if ($departmentId) {
            $qb->andWhere("d.id = ".$departmentId);
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,d,c')
            ->from($this->entity, 'r')
            ->join('r.department', 'd')
            ->join('r.category', 'c')
            ->orderBy('r.date', 'DESC');
        if ($this->startdate && $this->enddate) {
            $qb->andWhere("r.date >= '".$this->startdate."' and r.date<='".$this->enddate."'");
        }
        if ($this->department) {
            $qb->andWhere("d.id = ".$this->department);
        }
        if ($this->category) {
            $qb->andWhere("c.id = ".$this->category);
        }
        if ($this->comment) {
            $qb->andWhere("r.comment like '%".$this->comment."%'");
        }
        return $qb->getQuery()->getResult();
    }

    public function getSum()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->join('p.department', 'd')
            ->join('p.category', 'c')
            ->where('p.date >=\''.$this->startdate.'\' and p.date<=\''.$this->enddate.'\'');
        if ($this->department) {
            $qb->andWhere("d.id = ".$this->department);
        }
        if ($this->category) {
            $qb->andWhere("c.id = ".$this->category);
        }
        if ($this->comment) {
            $qb->andWhere("p.comment like '%".$this->comment."%'");
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $departmentId
     * @return mixed
     */
    public function getSumByCategory(string $dateFrom, string $dateTo, int $departmentId = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.money) as money, c.name as category, g.name as gr, g.id as gid, g.sortOrder as sort')
            ->from($this->entity, 'r')
            ->join('r.category', 'c')
            ->join('c.group', 'g')
            ->join('r.department', 'd')
            ->groupBy('r.category')
            ->andWhere("r.realdate >= :dateFrom and r.realdate <= :dateTo")
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ])
            ->orderBy('c.name');
        if ($departmentId) {
            $qb->andWhere("d.id = :departmentId")
                ->setParameter('departmentId', $departmentId);
        }
        return $qb->getQuery()->getResult();
    }

    public function getByCategoryAlias($dateFrom, $dateTo, $alias, $name = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.category', 'c')
            ->leftJoin('r.order', 'o')
            ->where('c.alias = :alias')
            ->andWhere("r.date >= :dateFrom and r.date <= :dateTo")
            ->setParameters([
                'alias' => $alias,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]);
        if ($name) {
            $qb->andWhere('r.comment like :comment')
                ->setParameter('comment', '%' . $name . '%');
        }
        return $qb->getQuery()->getResult();
    }
}
