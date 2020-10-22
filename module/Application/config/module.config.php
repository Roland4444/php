<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\ControllerFactory;
use Application\Job\DiamondCommissionJobFactory;
use Application\Log\SqlLogger;
use Application\Service\DiamondCommissionServiceFactory;
use Reference\Entity\User;
use Zend\Log\Logger;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use \Reference\Service\PasswordService;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'iframe' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/iframe',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'iframe',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'listeners' => [
        Listener\AuthenticationListener::class,
        Listener\Listener::class
    ],
    'service_manager' => [
        'invokables' => [
            Listener\AuthenticationListener::class => Listener\AuthenticationListener::class,
            Listener\Listener::class => Listener\Listener::class
        ],
        'factories' => [
            'diamondCommission' => DiamondCommissionServiceFactory::class,
            'diamondCommissionJob' => DiamondCommissionJobFactory::class,
            'accessService' => Service\AccessServiceFactory::class,
            'authlogService' => Service\AuthLog::class,
            'translator'    => \Zend\I18n\Translator\TranslatorServiceFactory::class,
            'navigation'    => \Zend\Navigation\Service\DefaultNavigationFactory::class,
            'my_sql_logger' => function () {
                $log = new Logger();
                $writer = new \Zend\Log\Writer\Stream('./data/logs/sql.log');
                $log->addWriter($writer);
                return new SqlLogger($log);
            },
        ],
        'abstract_factories' => [
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'shared/menu'             => __DIR__ . '/../view/shared/menu.phtml',
        ],
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
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Reference\Entity\User',
                'identity_property' => 'login',
                'credential_property' => 'password',
                'credential_callable' => function (User $user, $passwordGiven) {
                    $passwordData = PasswordService::getPasswordData($passwordGiven, $user->getPass());
                    return $user->getPassword() == $passwordData['password'];
                },
                //'credential_callable' => 'Reference\Entity\User::hashPassword',
            ],
        ],
    ],
    'log' => [
        'MyLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'priority' => Logger::DEBUG,
                    'options' => [
                        'stream' => __DIR__.'/../../../data/logs/errors.log',
                    ],
                ],
            ],
        ],
    ]
];
