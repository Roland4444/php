<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Storage\Entity\PurchaseDeal;

class PurchaseDealService
{
    private $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param int $id
     * @return PurchaseDeal
     */
    public function find(int $id): PurchaseDeal
    {
        return $this->dao->find($id);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->dao->findAll();
    }

    /**
     * @param object $deal
     * @return void
     */
    public function save(object $deal): void
    {
        $this->dao->save($deal);
    }

    /**
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        $this->dao->remove($id);
    }
}
