<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;
use Spare\Entity\Order;
use Spare\Entity\OrderPaymentStatus;
use Spare\Entity\OrderStatus;

/**
 * Class OrderRepository
 * @package Spare\Repositories
 */
class OrderRepository extends AbstractRepository
{
    /**
     * @param array|null $ids
     * @return array|null
     */
    public function findByIds($ids)
    {
        if (empty($ids)) {
            return null;
        }
        $ids = implode(',', $ids);

        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->select('s')
            ->from($this->getClassName(), 's')
            ->where("s.id IN ($ids)");

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Поиск заказов для индексной страницы
     *
     * @param array $params
     * @return mixed
     */
    public function getOrders($params)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->select('s, sl, st, t, p')
            ->from($this->getClassName(), 's')
            ->join('s.seller', 'sl')
            ->join('s.status', 'st')
            ->join('s.items', 't')
            ->join('t.spare', 'p')
            ->orderBy('s.date', 'desc')
            ->orderBy('s.id', 'desc');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $queryBuilder->andWhere("s.date >= :startdate and s.date <= :enddate")
            ->setParameters([
                'startdate' => $params['startdate'],
                'enddate' => $params['enddate'],
            ]);
        }
        if (! empty($params['seller'])) {
            $queryBuilder->andWhere("s.seller = :seller")
                ->setParameter('seller', $params['seller']);
        }

        if (! empty($params['orderStatus'])) {
            $queryBuilder->andWhere("s.status = :orderStatus")
            ->setParameter('orderStatus', $params['orderStatus']);
        }

        if (! empty($params['paymentStatus'])) {
            $queryBuilder->andWhere("s.paymentStatus = :paymentStatus")
                ->setParameter('paymentStatus', $params['paymentStatus']);
        }

        if (! empty($params['notReturnClose'])) {
            $queryBuilder->andwhere("st.alias <> '" . OrderStatus::ALIAS_CLOSED . "'");
        }

        if (! empty($params['number'])) {
            $queryBuilder->andwhere("s.id = :orderId")
                ->setParameter('orderId', $params['number']);
        }

        if (! empty($params['spare'])) {
            $queryBuilder->andWhere("t.spare = :spare")
            ->setParameter('spare', $params['spare']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getByPeriodAndSellerId(string $dateFrom, string $dateTo, int $sellerId = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('s, sl, st, t, p')
            ->from($this->getClassName(), 's')
            ->join('s.seller', 'sl')
            ->join('s.status', 'st')
            ->join('s.items', 't')
            ->join('t.spare', 'p')
            ->andWhere("s.date >= :startdate and s.date <= :enddate")
            ->setParameters([
                'startdate' => $dateFrom,
                'enddate' => $dateTo,
            ])
            ->andwhere("st.alias <> '" . OrderStatus::ALIAS_CLOSED . "'")
            ->orderBy('s.date', 'desc')
            ->orderBy('s.id', 'desc');

        if ($sellerId) {
            $qb->andWhere("s.seller = :seller")
                ->setParameter('seller', $sellerId);
        }
        return $qb->getQuery()->getResult();
    }


    private function getQueryBuilder()
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * Возвращает закупки по переданным item_id
     *
     * @param string $ids
     * @return mixed
     */
    public function getOrdersByIds($ids)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('s')
            ->from($this->getClassName(), 's')
            ->where("s.id IN ($ids)");
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Возвращает закупки по переданным item_id
     *
     * @param array $ids
     * @return mixed
     */
    public function getOrdersByExpenseIds($ids)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('s, ex, i')
            ->from($this->getClassName(), 's', 'id')
            ->join('s.expenses', 'ex')
            ->leftJoin('s.items', 'i')
            ->where("ex.id IN (:ids)")
            ->setParameter('ids', $ids);
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $ids
     * @param $status
     */
    public function updateStatus($ids, $status)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->update($this->getClassName(), 's')
            ->set('s.status', "'{$status}'")
            ->where("s.id IN ({$ids})");
        $queryBuilder->getQuery()->execute();
    }

    /**
     * Возвращает бронь со связанными таблицами
     *
     * @param $id
     * @return Order
     */
    public function findByIdWithPlanning($id)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('s, si, pi')
            ->from($this->getClassName(), 's')
            ->join('s.items', 'si')
            ->leftJoin('si.planningItem', 'pi')
            ->where("s.id = :id ")
            ->setParameter('id', $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Возвращает бронь со связанными таблицами
     *
     * @param $id
     * @return Order
     * @throws
     */
    public function findByIdForUpdatePaymentStatus($id)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('s, si, ex')
            ->from($this->getClassName(), 's')
            ->join('s.items', 'si')
            ->leftJoin('s.expenses', 'ex')
            ->where("s.id = :id ")
            ->setParameter('id', $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Получение данных только неоплаченных заказов для вывода в оплате запчастей
     *
     * @param null $sellerInn
     * @return mixed
     */
    public function getNotPaid($sellerInn = null)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->select('s, sel, i, spare')
            ->from($this->getClassName(), 's')
            ->join('s.seller', 'sel')
            ->join('s.items', 'i')
            ->join('i.spare', 'spare')
            ->join('s.paymentStatus', 'ps')
            ->orderBy('s.date', 'desc')
            ->addOrderBy('s.id', 'desc')
            ->where('ps.alias <> :alias')
            ->setParameter('alias', OrderPaymentStatus::PAID);
        if ($sellerInn) {
            $queryBuilder->andWhere('sel.inn = :inn')
                ->setParameter('inn', $sellerInn);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
