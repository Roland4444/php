<?php
namespace Application\Helper;

use Application\Service\AccessService;
use Zend\View\Helper\AbstractHelper;

class HasAccess extends AbstractHelper
{
    private AccessService $accessService;

    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function __invoke($controller, $action)
    {
        return $this->accessService->isAllowed($controller, $action);
    }
}
