<?php

namespace OfficeCash\Service;

use Core\Entity\AbstractEntity;
use Core\Service\AbstractService;
use OfficeCash\Facade\OfficeCash;
use OfficeCash\Repository\TransportIncomeRepository;

/**
 * Class TransportIncomeService
 * @package OfficeCash\Service
 * @method TransportIncomeRepository getRepository()
 */
class TransportIncomeService extends AbstractService
{
    private OfficeCash $transport;

    public function __construct($repository, $transport)
    {
        $this->transport = $transport;
        parent::__construct($repository);
    }

    public function getByPeriod(string $dateFrom, string $dateTo): array
    {
        return $this->getRepository()->getByPeriod($dateFrom, $dateTo);
    }

    public function save(AbstractEntity $row, $request = null)
    {
        $this->transport->saveIncomeFromTransport($row->getDate(), $row->getMoney());
        return parent::save($row, $request);
    }

    public function getMoneySum(string $dateFrom, string $dateTo)
    {
        return $this->getRepository()->getMoneySum($dateFrom, $dateTo);
    }
}
