<?php

namespace StorageTest\Controller\Mock;

use Storage\Controller\ContainerController;

class MockContainerController extends ContainerController
{
    use MockCurrentDepartment,
        MockRouteParams,
        MockParams,
        MockRequest,
        MockRedirect;
}
