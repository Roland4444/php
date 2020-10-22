<?php

namespace Reference\Repositories;

use Core\Repository\AbstractRepository;
use Core\Utils\Conditions;
use Core\Utils\Options;

abstract class AbstractReferenceRepository extends AbstractRepository
{
    /**
     * Get entity by default in options field
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDefaultByOption()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from($this->getClassName(), 'c');

        $qb->where('c.options like :default')
            ->setParameter('default', '%def%');

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Get entity not in archive
     * @return mixed
     */
    public function findNotArchival()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getClassName(), 'r')
            ->orderBy('r.name', 'ASC')
            ->where(Conditions::jsonNotContains('r', Options::ARCHIVAL));
        return $qb->getQuery()->getResult();
    }
}
