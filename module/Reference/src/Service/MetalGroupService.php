<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\MetalGroup;

/**
 * Class MetalGroupService
 * @package Reference\Service
 */
class MetalGroupService extends BaseService
{
    /**
     * MetalGroupService constructor.
     */
    public function __construct()
    {
        $this->setEntity(MetalGroup::class);
    }

    /**
     * @return mixed
     */
    public function findAllWithMetals()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('g, m')
            ->from($this->entity, 'g')
            ->join('g.metals', 'm')
            ->addOrderBy('g.name')
            ->addOrderBy('m.id');
        return $qb->getQuery()->getResult();
    }
}
