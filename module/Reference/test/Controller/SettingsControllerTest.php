<?php

namespace ReferenceTest\Controller;

use ApplicationTest\Controller\AbstractControllerTest;
use Reference\Controller\SettingsController;
use Reference\Entity\Settings;
use Reference\Form\SettingsForm;
use Reference\Service\SettingsService;

class SettingsControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/settings', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/settings/add', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/settings/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/settings/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/settings/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/settings');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/settings/delete/1', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/settings');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(SettingsController::class);
        $this->assertControllerClass('SettingsController');
        $this->assertMatchedRouteName('settings');
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new Settings();
        $mockEntity->setLabel('Комиссия');

        $mockService = $this->getMockBuilder(SettingsService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(SettingsService::class, $mockService);
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(SettingsForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(SettingsForm::class, $mockCategoryForm);
    }
}
