<?php

namespace Reference;

use Reference\Controller\Factory\SettingsControllerFactory;
use Reference\Service\WarehouseService;
use \Reference\Service\WarehouseServiceFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'department' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/department[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\DepartmentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'api_department_list' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api/department',
                    'defaults' => [
                        'controller' => Controller\DepartmentController::class,
                        'action' => 'list',
                    ],
                ],
            ],
            'employee' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/employee[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\EmployeeController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'customer' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/customer[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CustomerController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'customer_debt' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/customer-debt[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CustomerDebtController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'trader' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/trader[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TraderController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'reference\traderParent' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/trader-parent[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TraderParentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'categoryGroup' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/category-group[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CategoryGroupController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'category' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/category[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CategoryController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/reference/user',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit/[:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/delete/[:id]',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'delete',
                            ],
                        ],
                    ],
                    'changepass' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/changepass',
                            'defaults' => [
                                'action' => 'changepass',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'action' => 'logout',
                            ],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'metal' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/metal[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\MetalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'metalGroup' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/metal-group[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\MetalGroupController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'shipmentTariff' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/shipment-tariff[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShipmentTariffController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'containerOwner' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/container-owner[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ContainerOwnerController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'warehouse' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reference/warehouse[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\WarehouseController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'tech' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reference/vehicle[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\VehicleController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'settings' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/settings[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\SettingsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\UserController::class => Controller\Factory\UserControllerFactory::class,
            Controller\DepartmentController::class => Controller\Factory\DepartmentControllerFactory::class,
            Controller\SettingsController::class => SettingsControllerFactory::class,
            Controller\EmployeeController::class => Controller\Factory\EmployeeControllerFactory::class,
            Controller\CustomerController::class => ControllerFactory::class,
            Controller\CustomerDebtController::class => ControllerFactory::class,
            Controller\TraderController::class => ControllerFactory::class,
            Controller\TraderParentController::class => ControllerFactory::class,
            Controller\CategoryGroupController::class => Controller\Factory\CategoryGroupControllerFactory::class,
            Controller\CategoryController::class => Controller\Factory\CategoryControllerFactory::class,
            Controller\MetalController::class => ControllerFactory::class,
            Controller\MetalGroupController::class => Controller\Factory\MetalGroupControllerFactory::class,
            Controller\ShipmentTariffController::class => ControllerFactory::class,
            Controller\ContainerOwnerController::class => Controller\Factory\ContainerOwnerControllerFactory::class,
            Controller\VehicleController::class => ControllerFactory::class,
            Controller\WarehouseController::class => ControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\CategoryService::class => Service\CategoryServiceFactory::class,
            Form\CategoryForm::class => Form\Factory\CategoryFormFactory::class,
            Service\CategoryGroupService::class => Service\CategoryGroupService::class,
            Form\CategoryGroupForm::class => Form\Factory\CategoryGroupFormFactory::class,
            Service\CustomerService::class => Service\CustomerService::class,
            Service\CustomerDebtService::class => Service\CustomerDebtServiceFactory::class,
            Service\ContainerOwnerService::class => Service\ContainerOwnerService::class,
            Form\ContainerOwnerForm::class => Form\Factory\ContainerOwnerFormFactory::class,
            Service\DepartmentService::class => Service\DepartmentServiceFactory::class,
            Form\DepartmentForm::class => Form\Factory\DepartmentFormFactory::class,
            Service\SettingsService::class => Service\SettingsServiceFactory::class,
            Form\SettingsForm::class => Form\Factory\SettingsFormFactory::class,
            Service\EmployeeService::class => Service\EmployeeService::class,
            Form\EmployeeForm::class => Form\Factory\EmployeeFormFactory::class,
            Service\UserService::class => Service\UserService::class,
            Form\UserForm::class => Form\Factory\UserFormFactory::class,
            Service\MetalGroupService::class => Service\MetalGroupService::class,
            Form\MetalGroupForm::class => Form\Factory\MetalGroupFormFactory::class,
            Service\MetalService::class => Service\MetalServiceFactory::class,
            Service\ShipmentTariffService::class => Service\ShipmentTariffService::class,
            Service\TraderParentService::class => Service\TraderParentService::class,
            Service\TraderService::class => Service\TraderService::class,
            Service\VehicleService::class => Service\VehicleServiceFactory::class,
            Service\RoleService::class => Service\RoleService::class,
            WarehouseService::class => WarehouseServiceFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'Application_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'Reference\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
