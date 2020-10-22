<?php

namespace StorageTest\Controller\Mock;

use Storage\Controller\ShipmentController;

class MockShipmentController extends ShipmentController
{
    use MockCurrentDepartment,
        MockRouteParams,
        MockParams,
        MockCurrentUser,
        MockRedirect,
        MockHasAccess,
        MockRequest;

    private array $filterParams = [];

    public function setFilterParam($paramName, $paramValue): void
    {
        $this->filterParams[$paramName] = $paramValue;
    }

    protected function filterForm($request, $name)
    {
        return new class($this->filterParams){
            private array $filterParams;
            public function __construct($filterParams)
            {
                $this->filterParams = $filterParams;
            }
            public function getFilterParams(): array
            {
                return $this->filterParams;
            }
            public function getForm()
            {
                return null;
            }
        };
    }
}
