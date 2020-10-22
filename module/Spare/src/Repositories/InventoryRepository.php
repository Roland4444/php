<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;
use Spare\Entity\Inventory;

/**
 * Class InventoryRepository
 * @package Spare\Repositories
 */
class InventoryRepository extends AbstractRepository
{
    public function getTableListData($params)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s, t, p')
            ->from($this->getClassName(), 's')
            ->join('s.items', 't')
            ->join('t.spare', 'p')
            ->where('s.warehouse = :warehouseId')
            ->orderBy('s.id', 'DESC');

        if (! empty($params['startdate']) && ! empty($params['enddate'])) {
            $queryBuilder
                ->andWhere("s.date >= :startDate and s.date <= :endDate")
                ->setParameters([
                    'startDate' => $params['startdate'],
                    'endDate' => $params['enddate'],
                ]);
        }

        $queryBuilder->setParameter('warehouseId', $params['warehouseId']);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $warehouseId
     * @return Inventory
     * @throws
     */
    public function getLastInventory($warehouseId) : ?Inventory
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s')
            ->from($this->getClassName(), 's', 's.date')
            ->orderBy('s.date', 'DESC')
            ->setMaxResults(1)
            ->where("s.warehouse = :warehouseId")
            ->setParameters(['warehouseId' => $warehouseId]);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getItemExceedingDate(int $warehouseId, string $date)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('i')
            ->from($this->getClassName(), 'i')
            ->andWhere("i.warehouse = :warehouseId")
            ->andWhere("i.date > :date")
            ->setParameters([
                'warehouseId' => $warehouseId,
                'date' => $date
            ])
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getResult();
    }
}
