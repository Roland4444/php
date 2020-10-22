<?php

namespace Core;

use Core\Service\Factory\AccessValidateServiceFactory;
use Core\Service\Factory\ImageServiceFactory;

return [
    'service_manager' => [
        'aliases' => [
            'entityManager' => 'doctrine.entitymanager.orm_default',
        ],
        'factories' => [
            'dateService' => Service\DateService::class,
            'imageService' => ImageServiceFactory::class,
            'accessValidateService' => AccessValidateServiceFactory::class,
        ]
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
