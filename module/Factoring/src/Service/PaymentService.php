<?php

namespace Factoring\Service;

use Core\Entity\AbstractEntity;
use Core\Service\AbstractService;
use Factoring\Entity\Payment;
use Factoring\Repository\PaymentRepository;

/**
 * Class PaymentService
 * @package Factoring\Service
 * @method  PaymentRepository getRepository()
 */
class PaymentService extends AbstractService
{
    public function findByPeriod($dateStart, $dateEnd, $traderId)
    {
        return $this->getRepository()->findByPeriod($dateStart, $dateEnd, $traderId);
    }

    public function getSumFactoring($dateEnd, $bankId = null): ?float
    {
        return $this->getRepository()->getSumFactoring($dateEnd, $bankId);
    }

    public function getSumByPeriodGroupByBank(string $date): array
    {
        return $this->getRepository()->getSumByPeriodGroupByBank($date);
    }

    public function save(AbstractEntity $row, $request = null)
    {
        if ($row->getId() > 0 || ! $this->hasDuplicate($row)) {
            $row->setPaymentNumber(trim($row->getPaymentNumber()));
            return parent::save($row, $request);
        }
    }

    private function hasDuplicate(Payment $entity): bool
    {
        if ($entity->getPaymentNumber()) {
            $num = trim($entity->getPaymentNumber());
            return (bool)$this->getRepository()->findOneBy([
                'date' => $entity->getDate(),
                'payment_number' => $num
            ]);
        }
        return false;
    }

    public function getSumGroupByTrader($dateTo)
    {
        $sumByTrader = $this->getRepository()->getSumGroupByTrader($dateTo);
        $result = [];
        foreach ($sumByTrader as $item) {
            $result[$item['id']] = $item['sum'];
        }
        return $result;
    }
}
