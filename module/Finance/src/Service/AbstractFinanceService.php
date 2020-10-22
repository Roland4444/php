<?php

namespace Finance\Service;

use Application\Service\BaseService;

abstract class AbstractFinanceService extends BaseService
{
    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        if ($row->getId() > 0 || ! $this->hasDuplicate($row)) {
            $row->setPaymentNumber(trim($row->getPaymentNumber()));
            parent::save($row);
        }
    }

    protected function hasDuplicate($row)
    {
        if ($row->getPaymentNumber()) {
            $num = trim($row->getPaymentNumber());
            $qb = $this->em->createQueryBuilder();
            $qb->select('r')
                ->from($this->entity, 'r')
                ->where("r.payment_number = '".$num."'")
                ->andWhere("r.date = '".$row->getDate()."'");
            return count($qb->getQuery()->getResult()) > 0;
        }
        return false;
    }
}
