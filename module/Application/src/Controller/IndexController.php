<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\AuthLog;

class IndexController extends AbstractActionController
{
    protected $serviceLocator;

    public function __construct($container)
    {
        $this->serviceLocator = $container;
    }

    public function indexAction()
    {
        $authService = $this->serviceLocator->get('authenticationService');
        $identity = $authService->getIdentity();

        $logService = $this->serviceLocator->get('authlogService');
        $lastLogin = $logService->getLastLogin($identity->getLogin());

        return new ViewModel(['last_login' => $lastLogin]);
    }

    public function iframeAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }
}
