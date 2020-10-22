<?php

namespace Application\Controller\Plugin;

use Reference\Entity\Department;
use Reference\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CurrentUser extends AbstractPlugin
{
    private User $user;

    public function __construct($container)
    {
        /** @var AuthenticationService $authService */
        $authService = $container->get('authenticationService');
        $this->user = $authService->getIdentity();
    }

    public function isAdmin()
    {
        return $this->user->isAdmin();
    }

    public function isGlavbuh()
    {
        return $this->user->isGlavbuh();
    }

    public function getDepartment(): ?Department
    {
        return $this->user->getDepartment();
    }

    public function getRoleIds(): array
    {
        return $this->user->getRoleIds();
    }

    public function getRoleNames(): array
    {
        return $this->user->getRoleNames();
    }

    public function getWarehouses()
    {
        return $this->user->getWarehouses();
    }
}
