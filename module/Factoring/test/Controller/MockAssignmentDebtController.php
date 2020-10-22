<?php

namespace FactoringTest\Controller;

use Factoring\Controller\AssignmentDebtController;
use Factoring\Entity\AssignmentDebt;
use Zend\Http\Request;

class MockAssignmentDebtController extends AssignmentDebtController
{
    protected $request;
    private $mockCurrentDepartment;

    public function filterForm($request, $name)
    {
        return $this->getFilterForm();
    }

    protected function hasAccess($class, $action)
    {
    }

    public function setMockCurrentDepartment($mock): void
    {
        $this->mockCurrentDepartment = $mock;
    }

    protected function currentDepartment()
    {
        return $this->mockCurrentDepartment;
    }

    public function initRequest($method)
    {
        $request = new Request();
        $request->setMethod($method);
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    protected function getRouteId()
    {
        return 111;
    }

    protected function getEntityForEdit($id)
    {
        return new AssignmentDebt();
    }
}
