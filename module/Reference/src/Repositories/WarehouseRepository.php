<?php
namespace Reference\Repositories;

use Core\Repository\AbstractRepository;
use Reference\Entity\Warehouse;

class WarehouseRepository extends AbstractRepository
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

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('s')
            ->from($this->getClassName(), 's')
            ->where("s.id IN ($ids)");

        return $queryBuilder->getQuery()->getResult();
    }
}
