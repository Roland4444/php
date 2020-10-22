<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Zend\Db',
    'Zend\Mvc\Console',
    'Zend\Log',
    'Zend\I18n',
    'Zend\Session',
    'Zend\Cache',
    'Zend\Form',
    'Zend\Hydrator',
    'Zend\Router',
    'Zend\Validator',
    'Zend\Navigation',
    //'ZendDeveloperTools',
    'Core',
    'Application',
    'Modules',
    'Reference',
    'Reports',
    'Spare',
    'Storage',
    'DoctrineModule',
    'DoctrineORMModule',
    'RabbitMqModule\Module',
    'CurrentRoute',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Mvc\I18n',
    //---
    'Finance',
    'Api',
    'Factoring',
    'ShipmentDocs',
    'OfficeCash'
];
