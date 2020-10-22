<?php

namespace Core\Dao;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;

/**
 * Class AbstractDao
 * @package Core\Dao
 */
abstract class AbstractDao
{
    protected $entityClassName;
    private $repository;
    private EntityManager $entityManager;

    /**
     * AbstractDao constructor.
     * @param $entityManager
     * @throws Exception
     */
    public function __construct(EntityManager $entityManager)
    {
        if (! $this->entityClassName) {
            throw new InvalidArgumentException('$entityClassName is not specified');
        }
        $this->repository = $entityManager->getRepository($this->entityClassName);
        $this->entityManager = $entityManager;
    }

    /**
     * Get entity manager
     * @return mixed
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param int $id
     * @return null|object
     */
    public function find(int $id): ?object
    {
        return $this->repository->find($id);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->repository->findBy([]);
    }

    /**
     * @param object $entity
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $id): void
    {
        $row = $this->find($id);
        $this->entityManager->remove($row);
        $this->entityManager->flush();
    }
}
