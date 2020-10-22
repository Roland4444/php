<?php

namespace ProjectTest;

use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;

class Bootstrap
{
    /**
     * @var ServiceManager
     */
    public static $serviceManager;

    public static function go()
    {
        ini_set("memory_limit", "256M");
        chdir(dirname(__DIR__));

        include __DIR__ . './../vendor/autoload.php';

        $app = Application::init(include __DIR__ .
            '/../config/application.config.php');

        self::$serviceManager = $app->getServiceManager();
    }

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
    }
}

bootstrap::go();
