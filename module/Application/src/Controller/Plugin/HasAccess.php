<?php
namespace Application\Controller\Plugin;

use Application\Service\AccessService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class HasAccess extends AbstractPlugin
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
