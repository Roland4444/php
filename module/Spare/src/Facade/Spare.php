<?php

namespace Spare\Facade;

use OfficeCash\Facade\OfficeCash;

class Spare
{
    private OfficeCash $officeCash;

    public function __construct($officeCash)
    {
        $this->officeCash = $officeCash;
    }

    public function getExpensesByCategoryAlias($dateFrom, $dateTo, $alias, $name = null)
    {
        return $this->officeCash->getExpensesByCategoryAlias($dateFrom, $dateTo, $alias, $name);
    }
}
