<?php

namespace Modules\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;

class ContainerExtraRepository extends EntityRepository implements ObjectRepository
{
    public function getRentalGroupByCompany(string $dateFrom, string $dateTo): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('owner.id, sum(extra.ownerCost) as sum, count(extra.id) as count')
            ->from($this->getEntityName(), 'extra')
            ->leftJoin('extra.owner', 'owner')
            ->where("extra.dateFormal >= :dateFrom and extra.dateFormal <= :dateTo")
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ])
            ->groupBy('owner.id');

        return $qb->getQuery()->getResult();
    }
}
