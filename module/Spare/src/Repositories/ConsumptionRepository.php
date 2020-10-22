<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;

/**
 * Class ConsumptionRepository
 * @package Spare\Repositories
 */
class ConsumptionRepository extends AbstractRepository
{
    /**
     * Поиск расходов для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    public function findConsumptions($params)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s', 'i')
            ->from($this->getClassName(), 's')
            ->join('s.items', 'i')
            ->orderBy('s.date', 'desc')
            ->addOrderBy('s.id', 'desc');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $queryBuilder->andWhere("s.date >= '" . $params['startdate'] . "' and s.date <= '" . $params['enddate'] . "'");
        }

        if (! empty($params['employeeSpare'])) {
            $queryBuilder->andWhere("s.employee = " . $params['employeeSpare']);
        }

        if (! empty($params['spare'])) {
            $queryBuilder->andWhere("i.spare = " . $params['spare']);
        }

        if (! empty($params['vehicle'])) {
            $queryBuilder->andWhere("i.vehicle = " . $params['vehicle']);
        }

        if (! empty($params['warehouseId'])) {
            $queryBuilder->andWhere("s.warehouse = '{$params['warehouseId']}'");
        }

        if (! empty($params['comment'])) {
            $queryBuilder->andWhere("i.comment like :comment")
                ->setParameter('comment', '%' . $params['comment'] . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Возвращает список расходов по заданному складу с учетом даты последней инвентаризации
     *
     * @param $warehouseId
     * @param $dateStart
     * @return mixed
     */
    public function getTotalConsumption($warehouseId, $dateStart)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('r')
            ->from($this->getClassName(), 'r')
            ->where('r.warehouse = :warehouse')
            ->setParameter('warehouse', $warehouseId);

        if (! empty($dateStart)) {
            $queryBuilder->andWhere('r.date >= :date')
                ->setParameter('date', $dateStart);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
