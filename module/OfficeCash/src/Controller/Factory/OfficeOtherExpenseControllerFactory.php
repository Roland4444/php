<?php

namespace OfficeCash\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Interop\Container\ContainerInterface;
use Reference\Service\CategoryService;
use Reference\Service\DepartmentService;
use OfficeCash\Controller\OfficeOtherExpenseController;
use OfficeCash\Service\OtherExpenseService;

/**
 * Class OfficeOtherExpenseControllerFactory
 * @package Storage\Controller\Factory
 */
class OfficeOtherExpenseControllerFactory extends CrudControllerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $expenseService = $container->get(OtherExpenseService::class);
        $departmentService = $container->get(DepartmentService::class);
        $categoryService = $container->get(CategoryService::class);
        return new OfficeOtherExpenseController($entityManager, $expenseService, $departmentService, $categoryService);
    }
}
