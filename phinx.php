<?php
/**
 * Phinx default configureation file, return array
 */

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Define application env
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (_getenv() ? _getenv() : 'production'));

// Parse env from console arguments
// http://docs.phinx.org/en/latest/configuration.html#environments
function _getenv()
{
    global $argv;

    foreach ($argv as $key => $param)
    {
        if (preg_match("/^--environment=(?P<environment>[a-z_]+)$/", $param, $matches))
        {
            return $matches['environment'];
        }
        elseif (preg_match("/^-e$/", $param, $matches) && isset($argv[$key + 1]))
        {
            return $argv[$key + 1];
        }
    }
}

// Include application config
$config = APPLICATION_PATH . '/config/autoload/production.php';
$config = include $config;

$app = require __DIR__ . '/vendor/robmorgan/phinx/app/phinx.php';

// return Phinx configuration
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' =>
        [
            'default_migration_table' => 'phinxlog',
            'default_database' => APPLICATION_ENV,
            'production' => [
                'adapter' => 'mysql',
                'host' => $config['doctrine']['connection']['orm_default']['params']['host'],
                'name' => $config['doctrine']['connection']['orm_default']['params']['dbname'],
                'user' => $config['doctrine']['connection']['orm_default']['params']['user'],
                'pass' => $config['doctrine']['connection']['orm_default']['params']['password'],
                'port'=> 3306,
                'charset'=> 'utf8'
            ]
        ]
];