<?php
namespace Reports;

use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'reportProfit' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reports/profit[/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfitController::class,
                        'action' => 'index',
                    ],
                ]
            ],
            'reportDepExport' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reports/depexport[/[:action[/[:id]]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\DepExportController::class,
                        'action' => 'index',
                    ],
                    'may_terminate' => true,
                ]
            ],
            'reportExpenses' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reports/expenses[/clear/:clear]',
                    'defaults' => [
                        'controller' => Controller\ExpensesController::class,
                        'action' => 'index',
                    ],
                ]
            ],
            'reportExpensesLimits' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reports/limits',
                    'defaults' => [
                        'controller' => Controller\ExpensesController::class,
                        'action' => 'limits',
                    ],
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\ExpensesController::class => Controller\Factory\ExpensesControllerFactory::class,
            Controller\DepExportController::class => ControllerFactory::class,
            Controller\ProfitController::class => ControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'reports' => Facade\ReportsFactory::class,
            'RemoteSkladService' => Service\RemoteSkladService::class,
            'reportExpensesService'    => Service\Factory\ExpensesReportServiceFactory::class,
            Dao\RemoteSkladDao::class => Dao\RemoteSkladDaoFactory::class,
        ]
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
                    'Reports\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'upload_xls' => './data/excel.tmp',
];
