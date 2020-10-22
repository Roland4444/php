<?php
namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\TraderParent;

class TraderParentService extends BaseService
{
    /**
     * TraderParentService constructor.
     */
    public function __construct()
    {
        $this->setEntity(TraderParent::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->orderBy('r.order');
        return $qb->getQuery()->getResult();
    }
}
