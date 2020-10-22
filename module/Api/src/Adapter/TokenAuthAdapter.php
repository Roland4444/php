<?php

namespace Api\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class TokenAuthAdapter implements AdapterInterface
{
    private $identity;

    public function __construct()
    {
    }

    public function setUser($user)
    {
        $this->identity = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate()
    {
        return new Result(1, $this->identity);
    }
}
