<?php

namespace StorageTest\Controller\Mock;

use Zend\Http\Request as HttpRequest;
use Zend\Stdlib\Parameters;

trait MockRequest
{
    private bool $post = false;
    private array $data;

    public function setPost($post, $data = []): void
    {
        $this->post = (bool)$post;
        $this->data = $data;
    }

    public function getRequest(): HttpRequest
    {
        $request = new HttpRequest();
        if ($this->post) {
            $request->setMethod(HttpRequest::METHOD_POST);
            $params = new Parameters($this->data);
            $request->setPost($params);
        }
        return $request;
    }
}
