<?php

namespace StorageTest\Controller\Mock;

use Storage\Controller\ContainerItemController;

class MockContainerItemController extends ContainerItemController
{
    use MockCurrentDepartment,
        MockHasAccess,
        MockRouteParams,
        MockRequest,
        MockRedirect;
}
