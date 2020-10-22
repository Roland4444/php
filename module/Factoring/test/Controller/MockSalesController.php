<?php

namespace FactoringTest\Controller;

use Factoring\Controller\SalesController;

class MockSalesController extends SalesController
{
    public function filterForm($request, $name = 'myparams')
    {
        return $this->getFilterForm();
    }
}
