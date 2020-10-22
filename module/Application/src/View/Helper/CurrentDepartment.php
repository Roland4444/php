<?php

namespace Application\View\Helper;

use Doctrine\ORM\EntityRepository;
use Reference\Entity\Department;
use Zend\Authentication\AuthenticationService;
use Zend\Router\RouteMatch;
use Zend\View\Helper\AbstractHelper;

/**
 * Хелпер для получения текущей площадки в представлениях
 * Class CurrentDepartment
 * @package Application\Helper
 */
class CurrentDepartment extends AbstractHelper
{
    private EntityRepository $userService;

    private AuthenticationService $authService;

    private RouteMatch $routeMatch;

    private EntityRepository $departmentService;

    /**
     * CurrentDepartment constructor.
     * @param EntityRepository $userService
     * @param $authService
     * @param RouteMatch $routeMatch
     * @param EntityRepository $departmentService
     */
    public function __construct(EntityRepository $userService, $authService, RouteMatch $routeMatch, EntityRepository $departmentService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->routeMatch = $routeMatch;
        $this->departmentService = $departmentService;
    }

    /**
     * @return Department
     */
    public function __invoke(): ?Department
    {
        $userId = $this->authService->getIdentity()->getId();
        $user = $this->userService->find($userId);
        if ($user->getDepartment()) {
            return $user->getDepartment();
        }

        $departmentId = $this->routeMatch->getParam('department', null);
        if (empty($departmentId)) {
            return null;
        }
        return $this->departmentService->find($departmentId);
    }
}
