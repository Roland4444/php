<?php

namespace OfficeCash;

return [
    'router' => [
        'routes' => [
            'office_transport_income' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office-cash/transport-income[/:action][/:id]',
                    'constraints' => [
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'        => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TransportIncomeController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_cash_total' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/office/cash-total[/clear/:clear]',
                    'constraints' => [
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeCashTotalController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'office_other_expense' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/office/other-expense[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeOtherExpenseController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\TransportIncomeController::class => Controller\Factory\TransportIncomeControllerFactory::class,
            Controller\OfficeCashTotalController::class => Controller\Factory\OfficeCashTotalControllerFactory::class,
            Controller\OfficeOtherExpenseController::class => Controller\Factory\OfficeOtherExpenseControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\TransportIncomeService::class => Service\Factory\TransportIncomeServiceFactory::class,
            'officeCash' => Facade\OfficeCashFactory::class,
            Service\OtherExpenseService::class => Service\OtherExpenseService::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'Application_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'OfficeCash\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
