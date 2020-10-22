<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;

/**
 * Class OrderItemsRepository
 * @package Spare\Repositories
 */
class OrderItemsRepository extends AbstractRepository
{

    /**
     * Поиск броней по переданным позициям
     *
     * @param $ids
     * @return mixed|null
     */
    public function findByPositions($ids)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('r, o, s')
            ->from($this->getClassName(), 'r', 'r.id')
            ->join('r.order', 'o')
            ->join('o.seller', 's')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $ids);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Поиск заказов по id заявок
     *
     * @param $ids
     * @return mixed
     */
    public function findByIds($ids)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('r')
            ->from($this->getClassName(), 'r')
            ->where('r.planningItem IN (' . $ids . ')');
        return $queryBuilder->getQuery()->getResult();
    }
}
