<?php

namespace Modules\Service;

use Core\Service\AbstractService;
use Modules\Repository\TransportIncasRepository;

/**
 * Class TransportIncasService
 * @package Modules\Service
 * @method TransportIncasRepository getRepository()
 */
class TransportIncasService extends AbstractService
{
    public function getByPeriod(string $dateFrom, string $dateTo): array
    {
        return $this->getRepository()->getByPeriod($dateFrom, $dateTo);
    }

    public function getMoneySum(string $dateTo)
    {
        return $this->getRepository()->getMoneySum($dateTo);
    }
}
