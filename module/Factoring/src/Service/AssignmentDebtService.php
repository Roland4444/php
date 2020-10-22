<?php

namespace Factoring\Service;

use Core\Service\AbstractService;

class AssignmentDebtService extends AbstractService
{
    public function findByPeriod($dateStart, $dateEnd)
    {
        return $this->getRepository()->findByPeriod($dateStart, $dateEnd);
    }

    public function getSumFactoring($dateEnd, $bankId = null): ?float
    {
        return $this->getRepository()->getSumFactoring($dateEnd, $bankId);
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
