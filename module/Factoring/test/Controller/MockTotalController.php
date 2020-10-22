<?php

namespace FactoringTest\Controller;

use Factoring\Controller\TotalController;

class MockTotalController extends TotalController
{
    public function filterForm($request, $name = 'myparams')
    {
        return $this->getFilterForm();
    }
}
