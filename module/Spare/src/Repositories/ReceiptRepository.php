<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;
use Spare\Entity\Order;

/**
 * Class ReceiptRepository
 * @package Spare\Repositories
 */
class ReceiptRepository extends AbstractRepository
{
    /**
     * Поиск приходов для индексной страницы
     *
     * @param $params
     * @param int $warehouseId
     * @return mixed
     */
    public function findByParams($params, int $warehouseId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('s')
            ->from($this->getClassName(), 's')
            ->join('s.items', 'i')
            ->where('s.warehouse = :warehouseId')
            ->setParameter('warehouseId', $warehouseId)
            ->orderBy('s.date', 'desc')
            ->addOrderBy('s.id', 'desc');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $queryBuilder->andWhere("s.date >= '" . $params['startdate'] . "' and s.date <= '" . $params['enddate'] . "'");
        }

        if (! empty($params['seller'])) {
            $queryBuilder->andWhere("s.seller = '" . $params['seller'] . "'");
        }

        if (! empty($params['spare'])) {
            $queryBuilder->andWhere("i.spare = '{$params['spare']}'");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Возвращает список поступлениний по заданному складу с учетом даты последней инвентаризации
     *
     * @param $warehouseId
     * @param $dateStart
     * @return mixed
     */
    public function getTotalReceipt($warehouseId, $dateStart)
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
