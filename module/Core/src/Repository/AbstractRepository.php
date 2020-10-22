<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

abstract class AbstractRepository extends EntityRepository implements IRepository
{
    /**
     * @param object $entity
     *
     * @return void
     * @throws ORMException
     */
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param object $entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function merge(object $entity): void
    {
        $this->getEntityManager()->merge($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $id
     *
     * @return void
     * @throws ORMException
     */
    public function remove(int $id): void
    {
        $row = $this->find($id);
        $this->getEntityManager()->remove($row);
        $this->getEntityManager()->flush();
    }

    public function getReference(int $id)
    {
        return $this->getEntityManager()->getReference($this->getClassName(), $id);
    }
}
