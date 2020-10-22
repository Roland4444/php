<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => 'root',
                    'dbname' => 'avs',
                    'charset'  => 'utf8',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ],
                ],
            ],
        ],
    ],
];
