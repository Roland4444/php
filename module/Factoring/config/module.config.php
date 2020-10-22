<?php

namespace Factoring;

use Factoring\Controller\Factory\AssignmentDebtControllerFactory;
use Factoring\Controller\Factory\PaymentControllerFactory;
use Factoring\Controller\Factory\SalesControllerFactory;
use Factoring\Controller\Factory\TotalControllerFactory;
use Factoring\Form\AssignmentDebtFormFactory;
use Factoring\Form\PaymentFormFactory;
use Factoring\Service\Factory\AssignmentDebtServiceFactory;
use Factoring\Service\Factory\PaymentServiceFactory;
use Factoring\Service\Factory\ProviderServiceFactory;
use Factoring\Service\Factory\SalesServiceFactory;

return [
    'router' => [
        'routes' => [
            'factoring_sales' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/factoring/sales[/clear/:clear]',
                    'defaults' => [
                        'controller' => Controller\SalesController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'factoring_payments' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/factoring/payments[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\PaymentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'factoring_total' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/factoring/total[/clear/:clear]',
                    'defaults' => [
                        'controller' => Controller\TotalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'factoring_assignment_debt' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/factoring/assignment-debt[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssignmentDebtController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\SalesController::class => SalesControllerFactory::class,
            Controller\PaymentController::class => PaymentControllerFactory::class,
            Controller\TotalController::class => TotalControllerFactory::class,
            Controller\AssignmentDebtController::class => AssignmentDebtControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'factoring' => Facade\FactoringFactory::class,
            'factoringProviderService' => ProviderServiceFactory::class,
            'factoringSalesService' => SalesServiceFactory::class,
            'factoringPaymentService' => PaymentServiceFactory::class,
            'factoringAssignmentDebtService' => AssignmentDebtServiceFactory::class,
            'factoringAssignmentDebtForm' => AssignmentDebtFormFactory::class,
            'factoringPaymentForm' => PaymentFormFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
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
                    __NAMESPACE__ . '\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
