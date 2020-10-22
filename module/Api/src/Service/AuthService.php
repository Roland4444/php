<?php

namespace Api\Service;

use Reference\Service\UserService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;

class AuthService
{
    private $userService;
    private $adapter;
    private $authService;

    public function __construct(UserService $userService, AdapterInterface $adapter, AuthenticationService $authService)
    {
        $this->userService = $userService;
        $this->adapter = $adapter;
        $this->authService = $authService;
    }

    /**
     * @param string $token
     *
     * @throws \Exception
     */
    public function authByToken(string $token)
    {
        $user = $this->userService->findByToken($token);
        if (empty($user)) {
            throw new \Exception('User not found');
        }
        if ($user->getIsBlocked()) {
            throw new \Exception('User was blocked');
        }
        $expiredTime = $user->getTokenExpired();
        $currentRime = new \DateTime();
        if ($expiredTime->getTimestamp() < $currentRime->getTimestamp()) {
            throw new \Exception('Token has expired');
        }
        $this->adapter->setUser($user);
        $this->authService->setAdapter($this->adapter);
        $this->authService->authenticate();
    }
}
