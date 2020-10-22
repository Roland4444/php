<?php
namespace Modules;

use Application\Controller\ControllerFactory;
use Modules\Controller\Factory\WaybillsControllerFactory;
use Modules\Controller\Factory\CompletedVehicleTripsControllerFactory;
use Modules\Controller\Factory\ScheduledVehicleTripsControllerFactory;
use Modules\Service\Factory\CompletedVehicleTripsServiceFactory;
use Modules\Service\Factory\ScheduledVehicleTripsServiceFactory;
use Reports\Service\RemoteSkladService;

return [
    'router' => [
        'routes' => [
            'completedVehicleTrips' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/completed-vehicle-trips[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'        => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CompletedVehicleTripsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'scheduledVehicleTrips' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/scheduled-vehicle-trips[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'        => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ScheduledVehicleTripsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'waybills' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/waybills[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\WaybillsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'waybillsReport' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/waybills-report[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\WaybillsReportController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'waybillSettings' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/waybill-settings[/:action][/:id]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\WaybillSettingsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'containerRental' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/modules/rental[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ContainerRentalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'transportIncome' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/transport/income[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\IncomeController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'transportIncas' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/transport/incas[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\IncasController::class,
                        'action' => 'index',
                    ],
                ],
            ]
        ]
    ],

    'controllers' => [
        'factories' => [
            Controller\CompletedVehicleTripsController::class => CompletedVehicleTripsControllerFactory::class,
            Controller\ScheduledVehicleTripsController::class => ScheduledVehicleTripsControllerFactory::class,
            Controller\WaybillsController::class => WaybillsControllerFactory::class,
            Controller\ContainerRentalController::class => ControllerFactory::class,
            Controller\WaybillsReportController::class => ControllerFactory::class,
            Controller\WaybillSettingsController::class => ControllerFactory::class,
            Controller\IncomeController::class => Controller\Factory\IncomeControllerFactory::class,
            Controller\IncasController::class => Controller\Factory\IncasControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\CompletedVehicleTripsService::class => CompletedVehicleTripsServiceFactory::class,
            Service\ScheduledVehicleTripsService::class => ScheduledVehicleTripsServiceFactory::class,
            Service\WaybillsService::class => Service\WaybillsService::class,
            Service\WaybillSettingsService::class => Service\WaybillSettingsService::class,
            RemoteSkladService::class => RemoteSkladService::class,
            Service\ContainerRentalService::class => Service\Factory\ContainerRentalServiceFactory::class,
            Service\TransportIncasService::class => Service\Factory\TransportIncasServiceFactory::class,
            Facade\Transport::class => Facade\TransportFactory::class,
        ]
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
                    'Modules\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
