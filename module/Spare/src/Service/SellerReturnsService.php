<?php

namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Entity\SellerReturn;

class SellerReturnsService extends AbstractService
{
    private $sellerService;

    /**
     * SellerReturnsService constructor.
     * @param $repository
     * @param SellerService $sellerService
     */
    public function __construct($repository, $sellerService)
    {
        parent::__construct($repository);
        $this->sellerService = $sellerService;
    }

    public function getAll(array $params = null)
    {
        return $this->getRepository()->getAll($params);
    }

    /**
     * Добавить новый возврат поставщика
     * @param $data
     * @param $sellerReturn
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function store($data, SellerReturn $sellerReturn)
    {
        $seller = $this->sellerService->find($data->seller);

        $sellerReturn->setDate($data->date);
        $sellerReturn->setMoney($data->money);
        $sellerReturn->setSeller($seller);

        $this->getRepository()->save($sellerReturn);

        return $sellerReturn;
    }
}
