<?php

namespace Application\View\Helper\Factory;

use Application\View\Helper\CurrentDepartment;
use Interop\Container\ContainerInterface;
use Reference\Entity\Department;
use Reference\Entity\User;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Фабрика для создания хелпера CurrentDepartment
 * Class CurrentDepartmentFactory
 * @package Application\Helper
 */
final class CurrentDepartmentFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $userService = $entityManager->getRepository(User::class);
        $authService = $container->get('authenticationService');
        $departmentService = $entityManager->getRepository(Department::class);
        $routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();

        return new CurrentDepartment($userService, $authService, $routeMatch, $departmentService);
    }
}
