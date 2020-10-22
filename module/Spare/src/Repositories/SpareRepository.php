<?php
namespace Spare\Repositories;

use Core\Repository\AbstractRepository;

/**
 * Class SpareRepository
 * @package Spare\Repositories
 */
class SpareRepository extends AbstractRepository
{
    public function findByParams(array $params)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')->from($this->getClassName(), 'r')
            ->orderBy('r.name', 'ASC');

        if (! empty($params['name'])) {
            $qb->where('r.name like :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findRawWithImages($params)
    {
        $conn = $this->getEntityManager()->getConnection();

        $entity = str_replace('\\', '\\\\\\', $this->getClassName());
        $sql = 'SELECT s.id, s.name, s.is_composite, s.comment, s.units, i.filename FROM spares s'
            .' LEFT JOIN images i ON i.entity="'.$entity.'" and i.entity_id=s.id';

        if (! empty($params['name'])) {
            $sql .= ' WHERE s.name like ' .$conn->quote('%'.$params['name'].'%'). '';
        }
        $sql .= ' ORDER BY s.name ASC';
        $rows = $conn->query($sql);
        return $rows->fetchAll();
    }
}
