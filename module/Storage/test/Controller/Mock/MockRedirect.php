<?php

namespace StorageTest\Controller\Mock;

use Zend\Http\Response;

trait MockRedirect
{
    protected function redirect()
    {
        return new class{
            public function toRoute($route, $params): Response
            {
                $response = new Response();
                $response->setStatusCode(Response::STATUS_CODE_301);
                return $response;
            }
        };
    }
}
