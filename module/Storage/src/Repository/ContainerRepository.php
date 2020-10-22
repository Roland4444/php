<?php

namespace Storage\Repository;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;
use Reference\Entity\Department;

class ContainerRepository extends AbstractRepository
{
    public function findByOwnerAndDates($owner, $startDate, $endDate)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r, s, e')
            ->from($this->getClassName(), 'r')
            ->join('r.shipment', 's')
            ->join('r.extraOwner', 'e')
            ->where('e.ownerCost <> 0')
            ->orderBy('s.date', 'DESC')
            ->orderBy('e.dateFormal', 'DESC');
        if ($owner) {
            $qb->andWhere('e.owner = ' .$owner);
        }
        if ($startDate && $endDate) {
            $qb->andWhere("e.dateFormal >= '".$startDate."' and e.dateFormal<='".$endDate."'");
        }

        return $qb->getQuery()->getResult();
    }

    public function getDuplicate($post, $shipmentId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r,s')
            ->from($this->getClassName(), 'r')
            ->join('r.shipment', 's')
            ->where("r.name = '" . $post['name'] . "'")
            ->andWhere('s.id = ' . $shipmentId);

        return $qb->getQuery()->getResult();
    }

    public function getOwnerCostSumByDate($startDate, $endDate)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(e.ownerCost)')
            ->from($this->getClassName(), 'p')
            ->join('p.extraOwner', 'e')
            ->join('p.shipment', 's');
        if ($startDate && $endDate) {
            $qb->andWhere("s.date >= '".$startDate."' and s.date<='".$endDate."'");
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSumByOwnerFormalOrdered(?int $ownerId, ?string $startDate, ?string $endDate)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sum(e.ownerCost)')
            ->from($this->getClassName(), 'p')
            ->join('p.extraOwner', 'e')
            ->join('p.shipment', 's');
        if ($ownerId) {
            $qb->where("e.owner = '".$ownerId."'");
        }
        if ($startDate && $endDate) {
            $qb->andWhere("e.dateFormal >= '".$startDate."' and e.dateFormal<='".$endDate."'");
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getColorDepartmentsContainersByDate($date)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from($this->getClassName(), 'c')
            ->join('c.shipment', 's')
            ->join('s.department', 'd')
            ->andWhere('d.type = :type')
            ->setParameter('type', Department::TYPE_COLOR)
            ->andWhere('s.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }
}
