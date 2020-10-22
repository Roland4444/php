<?php
namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\Role;

class RoleService extends BaseService
{
    /**
     * RoleService constructor.
     */
    public function __construct()
    {
        $this->setEntity(Role::class);
    }

    public function findDefault()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.id = 1');
        return $qb->getQuery()->getSingleResult();
    }
}
