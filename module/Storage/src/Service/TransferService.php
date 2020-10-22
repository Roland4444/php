<?php

namespace Storage\Service;

use Application\Exception\ServiceException;
use Core\Entity\AbstractEntity;
use Core\Service\AbstractService;
use Storage\Entity\TransferList;
use Core\Repository\IRepository;
use Storage\Repository\TransferRepository;

class TransferService extends AbstractService
{
    private $remoteSkladDao;

    public function __construct(IRepository $transferRepo, $remoteSkladDao)
    {
        $this->remoteSkladDao = $remoteSkladDao;
        parent::__construct($transferRepo);
    }

    public function getBalance(string $startDate, string $endDate, int $departmentId): array
    {
        $balance = $this->getRepository()->getBalance($startDate, $endDate, $departmentId);
        $result = [];
        foreach ($balance as $item) {
            $result[$item['id']] = $item;
        }
        return  $result;
    }

    public function getTransferList($params)
    {
        $items = $this->getRepository()->getTableListData($params);
        return new TransferList($items);
    }

    public function getAvgSor(array $params)
    {
        return $this->remoteSkladDao->getAvgSor($params);
    }

    /**
     * {@inheritDoc}
     */
    public function save(AbstractEntity $transfer, $request = null)
    {
        if ($transfer->getNakl1()) {
            $item = $this->remoteSkladDao
                ->getByDateDepartmentAndWaybill($transfer->getDate(), $transfer->getSource()->getName(), $transfer->getNakl1());
            if ($item) {
                $transfer->setWeight(abs($item->getMassa()));
            } else {
                throw new ServiceException("Неверный номер накладной отправителя");
            }
        }

        if ($transfer->getNakl2()) {
            $item = $this->remoteSkladDao
                ->getByDateDepartmentAndWaybill($transfer->getDate(), $transfer->getDest()->getName(), $transfer->getNakl2());

            if ($item) {
                $transfer->setActual(abs($item->getMassa()));
            } else {
                throw new ServiceException("Неверный номер накладной получателя");
            }
        }

        $this->getRepository()->save($transfer);

        return true;
    }
}
