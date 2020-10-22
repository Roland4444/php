<?php

namespace StorageTest\Controller\Mock;

use Storage\Controller\PurchaseDealController;

class MockPurchaseDealController extends PurchaseDealController
{
    use MockRequest,
        MockRouteParams,
        MockCurrentDepartment,
        MockRedirect;
}
