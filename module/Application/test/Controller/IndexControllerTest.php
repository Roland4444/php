<?php

namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Application\Entity\AuthLog;

class IndexControllerTest extends AbstractControllerTest
{
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class);
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }

    public function testIndexActionViewModelTemplateRenderedWithinLayout()
    {
        $this->dispatch('/', 'GET');
        $this->assertQuery('.main #contentBlock');
    }

    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }

    public function setUp(): void
    {
        parent::setUp();

        $lastLogin = new AuthLog();
        $lastLogin->setLogin('admin');
        $lastLogin->setDate('2019-01-01 10:23:15');
        $mockAuthLogService = $this->getMockBuilder(\Application\Service\AuthLog::class)->disableOriginalConstructor()->getMock();
        $mockAuthLogService->expects($this->any())->method('getLastLogin')->willReturn($lastLogin);

        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('authlogService', $mockAuthLogService);
        $this->getApplicationServiceLocator()->setAllowOverride(false);
    }
}
