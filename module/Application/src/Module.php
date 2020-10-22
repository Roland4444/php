<?php

namespace Application;

use Application\Helper\InitScript;
use Application\View\Helper\IsNormalLimitMonth;
use Application\Helper\HasAccessFactory;
use Application\View\Helper\ViewHelper;
use Application\View\Helper\Factory\ViewHelperFactory;
use Application\View\Helper\CurrentDepartment;
use Application\View\Helper\Factory\CurrentDepartmentFactory;
use Interop\Container\ContainerInterface;
use Zend\Authentication\Storage\Session;
use Zend\I18n\Translator\Resources;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\SessionManager;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        setlocale(LC_ALL, "ru_RU.utf8");

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Перевод сообщений валидатора
        $this->configureValidationMessagesTranslation($e);
    }

    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'currentUser' => function (ServiceManager $serviceManager) {
                    return new Controller\Plugin\CurrentUser($serviceManager);
                },
                'hasAccess' => function (ServiceManager $container) {
                    $accessService = $container->get('accessService');
                    return new Controller\Plugin\HasAccess($accessService);
                },
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'authenticationService' => function (ContainerInterface $container) {
                    return $container->get('doctrine.authenticationservice.orm_default');
                },

            ]
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'viewHelper' => ViewHelper::class,
                'currentDepartment' => CurrentDepartment::class,
                'isNormalLimitMonth' => IsNormalLimitMonth::class,],
            'factories' => [
                ViewHelper::class => ViewHelperFactory::class,
                'hasAccess' => HasAccessFactory::class,
                CurrentDepartment::class => CurrentDepartmentFactory::class,
                IsNormalLimitMonth::class => InvokableFactory::class,
                'initScript' => fn () => new InitScript(),
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return ['Zend\Loader\StandardAutoloader' => ['namespaces' => [__NAMESPACE__ => __DIR__ .
            '/src/' . __NAMESPACE__,],],];
    }

    private function configureValidationMessagesTranslation(MvcEvent $e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('MvcTranslator');
        $translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            Resources::getPatternForValidator()
        );
        AbstractValidator::setDefaultTranslator($translator);
    }
}
