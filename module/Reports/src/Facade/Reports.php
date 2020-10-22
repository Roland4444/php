<?php

namespace Reports\Facade;

use OfficeCash\Facade\OfficeCash;

class Reports
{
    private OfficeCash $officeCash;

    public function __construct($officeCash)
    {
        $this->officeCash = $officeCash;
    }

    public function getOfficeExpenseSumByCategory(string $dateFrom, string $dateTo)
    {
        return $this->officeCash->getSumByCategory($dateFrom, $dateTo);
    }
}
