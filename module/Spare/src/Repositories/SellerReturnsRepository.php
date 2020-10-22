<?php

namespace Spare\Repositories;

use Core\Repository\AbstractRepository;

class SellerReturnsRepository extends AbstractRepository
{
    public function getAll($params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sr, s')
            ->from($this->getClassName(), 'sr')
            ->join('sr.seller', 's');

        if (! empty($params['startDate']) && ! empty($params['endDate'])) {
            $qb->andWhere("sr.date >= :startDate and sr.date <= :endDate")
                ->setParameters([
                    'startDate' => $params['startDate'],
                    'endDate' => $params['endDate'],
                ]);
        }

        if (! empty($params['seller'])) {
            $qb->andWhere("sr.seller = :seller")->setParameter('seller', $params['seller']);
        }

        return $qb->getQuery()->getResult();
    }
}
