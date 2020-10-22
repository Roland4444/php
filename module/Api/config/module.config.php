<?php

namespace Api;

use Api\Adapter\TokenAuthAdapterFactory;
use Api\Controller\AuthControllerFactory;
use Api\Service\AuthServiceFactory;

return [
    'router' => [
        'routes' => [
            'token' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/token',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'token',
                    ]
                ]
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => AuthControllerFactory::class,
        ]
    ],
    'service_manager' => [
        'factories' => [
            'tokenAuthAdapter' => TokenAuthAdapterFactory::class,
            'authService' => AuthServiceFactory::class,
        ]
    ]
];
