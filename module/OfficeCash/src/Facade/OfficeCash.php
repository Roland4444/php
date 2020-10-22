<?php

namespace OfficeCash\Facade;

use Modules\Facade\Transport;
use OfficeCash\Service\OtherExpenseService;

class OfficeCash
{
    private Transport $transport;
    private OtherExpenseService $expenseService;

    public function __construct($transport, $expenseService)
    {
        $this->transport = $transport;
        $this->expenseService = $expenseService;
    }

    /**
     * Сохранить так же в таблицу модуля Modules
     * @param $date
     * @param $money
     */
    public function saveIncomeFromTransport($date, $money): void
    {
        $this->transport->saveIncomeFromTransport($date, $money);
    }

    public function getExpensesByCategoryAlias($dateFrom, $dateTo, $alias, $name = null)
    {
        return $this->toArray($this->expenseService->getByCategoryAlias($dateFrom, $dateTo, $alias, $name));
    }

    public function getSumByCategory(string $dateFrom, string $dateTo)
    {
        return $this->expenseService->getSumByCategory($dateFrom, $dateTo);
    }

    private function toArray($arrayOfObjects): array
    {
        return json_decode(json_encode($arrayOfObjects), true);
    }

    public function getExpenseTotalSumByDepartment(string $dateFrom, $dateTo, ?int $departmentId)
    {
        return $this->expenseService->getTotalSumByDepartment($dateFrom, $dateTo, $departmentId);
    }
}
