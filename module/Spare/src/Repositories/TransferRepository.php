<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;
use Doctrine\ORM\Query;
use Spare\Entity\Spare;

/**
 * Class TransferRepository
 * @package Spare\Repositories
 */
class TransferRepository extends AbstractRepository
{
    /**
     * Поиск перебросок для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    public function findTransfers($params)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s')
            ->from($this->getClassName(), 's')
            ->orderBy('s.date', 'desc')
            ->addOrderBy('s.id', 'desc');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $queryBuilder->andWhere("s.date >= '" . $params['startdate'] . "' and s.date <= '" . $params['enddate'] . "'");
        }

        if (! empty($params['spare'])) {
            $queryBuilder->andWhere("s.spare = " . $params['spare']);
        }
        if (! empty($params['warehouseId'])) {
            $queryBuilder->andWhere(
                "s.source = '{$params['warehouseId']}' OR s.dest = '{$params['warehouseId']}'"
            );
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $warehouseId
     * @param $dateStart
     * @return mixed|mixed[]
     * @throws
     */
    public function getTotalTransfers($warehouseId, $dateStart)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sp.id as spare_id, sp.name as spare_name, sp.units as spare_units, sum(t.quantity) as sum, s.id as source, d.id as dest')
            ->from($this->getClassName(), 't')
            ->join('t.spare', 'sp')
            ->join('t.source', 's')
            ->join('t.dest', 'd')
            ->where('s.id = '.$warehouseId.' OR d.id = '.$warehouseId)
            ->groupBy('sp.id')
            ->addGroupBy('s.id')
            ->addGroupBy('d.id');

        if (! empty($dateStart)) {
            $qb->andWhere('t.date >= :date')
                ->setParameter('date', $dateStart);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
