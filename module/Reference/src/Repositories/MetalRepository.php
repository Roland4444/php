<?php

namespace Reference\Repositories;

use Core\Repository\AbstractRepository;

class MetalRepository extends AbstractRepository
{
    public function findByGroupType($isFerrous)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->getClassName(), 'r')
            ->join('r.group', 'g')
            ->orderBy('r.name', 'ASC');

        if ($isFerrous) {
            $qb->where("g.ferrous = 1");
        } else {
            $qb->where("g.ferrous = 0");
        }
        return $qb->getQuery()->getResult();
    }

    public function clearDef()
    {
        $query = $this->getEntityManager()->createQuery("UPDATE ".$this->getClassName()." s SET s.def = '0'");
        $query->execute();
    }
}
