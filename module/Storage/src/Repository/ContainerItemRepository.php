<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\NonUniqueResultException;
use Storage\Entity\ContainerItem;

class ContainerItemRepository extends AbstractRepository
{
    public function getActualByMetal(string $dateFrom, string $dateTo, $department)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m.id, sum(p.realWeight) as sum')
            ->from($this->getClassName(), 'p')
            ->join('p.metal', 'm')
            ->join('p.container', 'c')
            ->join('c.shipment', 's')
            ->join('s.department', 'd')
            ->where("s.date >= :dateFrom and s.date <= :dateTo")
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ])->groupBy('p.metal');
        if ($department) {
            $qb->andWhere("d.id = " . $department);
        }
        return $qb->getQuery()->getResult();
    }

    public function getSubtracting($deps)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m.id, sum(p.weight - p.realWeight) as sub')
            ->from($this->getClassName(), 'p')
            ->join('p.metal', 'm')
            ->join('p.container', 'c')
            ->join('c.shipment', 's')
            ->join('s.department', 'd')
            ->groupBy('m');
        foreach ($deps as $dep) {
            $qb->orWhere("d.id = " . $dep->getId());
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     * @return ContainerItem
     * @throws NonUniqueResultException
     */
    public function findByItemId(int $id): ContainerItem
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('i,c,s')
            ->from($this->getClassName(), 'i')
            ->join('i.container', 'c')
            ->join('c.shipment', 's')
            ->where('i.id = :itemId')
            ->setParameter('itemId', $id);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
