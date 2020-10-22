<?php

namespace ShipmentDocs;

use ShipmentDocs\Service\ApiService;

return [
    'router' => [
        'routes' => [
            'shipmentDocs' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/shipment-docs',
                    'defaults' => [
                        'controller' => Controller\DocumentController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'document' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/document[/clear/:clear][/:action][/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => Controller\DocumentController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'driver' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/driver[/clear/:clear][/:action][/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => Controller\DriverController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'owner' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/owner[/clear/:clear][/:action][/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => Controller\OwnerController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'receiver' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/receiver[/clear/:clear][/:action][/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => Controller\ReceiverController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'not_available' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/not_available',
                            'defaults' => [
                                'controller' => Controller\DocumentController::class,
                                'action' => 'notAvailable',
                            ],
                        ],
                    ],
                    'requisites' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/requisites',
                            'defaults' => [
                                'controller' => Controller\RequisitesController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'dosimeter' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/dosimeter',
                            'defaults' => [
                                'controller' => Controller\DosimeterController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'representative' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/representative',
                            'defaults' => [
                                'controller' => Controller\RepresentativeController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ]
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\DocumentController::class => Controller\Factory\ControllerFactory::class,
            Controller\DriverController::class => Controller\Factory\ControllerFactory::class,
            Controller\OwnerController::class => Controller\Factory\ControllerFactory::class,
            Controller\ReceiverController::class => Controller\Factory\ControllerFactory::class,
            Controller\RequisitesController::class => Controller\Factory\ControllerFactory::class,
            Controller\DosimeterController::class => Controller\Factory\ControllerFactory::class,
            Controller\RepresentativeController::class => Controller\Factory\ControllerFactory::class,
        ]
    ],
    'service_manager' => [
        'factories' => [
            ApiService::class => Service\ServiceFactory::class
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
