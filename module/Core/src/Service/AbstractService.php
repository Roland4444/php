<?php

namespace Core\Service;

use Core\Entity\AbstractEntity;
use Core\Repository\IRepository;

/**
 * Class AbstractService
 *
 * @package Core\Service
 */
abstract class AbstractService
{
    private IRepository $repository;

    /**
     * Order for findAll method.
     */
    protected array $order = [];

    /**
     * Constructor.
     *
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get repository instance.
     * @return IRepository
     */
    protected function getRepository(): IRepository
    {
        return $this->repository;
    }

    /**
     * Get entity class name.
     * @return string
     */
    public function getEntity()
    {
        return $this->getRepository()->getClassName();
    }

    /**
     * Find entity by id.
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Find all entity
     * @return mixed
     */
    public function findAll()
    {
        return $this->getRepository()->findBy([], $this->order);
    }

    /**
     * Save entity to db.
     * @param AbstractEntity $row
     * @param null $request
     * @return bool
     */
    public function save(AbstractEntity $row, $request = null)
    {
        $this->getRepository()->save($row);
        return true;
    }

    public function merge(AbstractEntity $row)
    {
        $this->getRepository()->merge($row);
    }

    /**
     * @param int $id
     */
    public function remove(int $id) : void
    {
        $this->getRepository()->remove($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getReference(int $id)
    {
        return $this->getRepository()->getReference($id);
    }

    //todo надо вынести это в какой нибудь ReferenceService т к этот метод нужен почти для всех справочников и только для них
    /**
     * Find default entity
     * @return mixed
     */
    public function findDefault()
    {
        return $this->getRepository()->findOneBy(['def' => 1]);
    }
}
