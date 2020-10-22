<?php

namespace Storage\Plugin;

use Reference\Entity\Department;
use Reference\Entity\User;
use Reference\Service\DepartmentService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CurrentDepartment extends AbstractPlugin
{
    private DepartmentService $departmentService;
    private AuthenticationService $authService;

    public function __construct($departmentService, $authService)
    {
        $this->departmentService = $departmentService;
        $this->authService = $authService;
    }

    public function getDepartment(): ?Department
    {
        if (! $this->getId()) {
            return null;
        }
        return $this->departmentService->find($this->getId());
    }

    public function getId()
    {
        $user = $this->getAuthUser();

        if ($user->getDepartment()) {
            return $user->getDepartment()->getId();
        }
        return $this->getController()->getEvent()->getRouteMatch()->getParam('department', null);
    }

    private function getAuthUser(): User
    {
        return $this->authService->getIdentity();
    }

    public function isHide(): bool
    {
        $department = $this->getDepartment();
        return $department && $department->isHide();
    }
}
