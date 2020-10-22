<?php

namespace Storage\Plugin;

use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class CurrentDepartmentFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $departmentService = $container->get(DepartmentService::class);
        $authService = $container->get('authenticationService');
        return new CurrentDepartment($departmentService, $authService);
    }
}
