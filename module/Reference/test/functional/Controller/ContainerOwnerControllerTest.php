<?php

namespace ApplicationTest\Controller;

use Reference\Controller\ContainerOwnerController;
use Reference\Entity\ContainerOwner;
use Reference\Form\ContainerOwnerForm;
use Reference\Service\ContainerOwnerService;

class ContainerOwnerControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/container-owner', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockEntity = new ContainerOwner();
        $this->mockService([$mockEntity, $mockEntity]);

        $this->dispatch('/reference/container-owner', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/container-owner/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/container-owner/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/container-owner/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/container-owner');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/container-owner/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/container-owner/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/container-owner/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/container-owner');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/container-owner/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/container-owner');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(ContainerOwnerController::class);
        $this->assertControllerClass('ContainerOwnerController');
        $this->assertMatchedRouteName('containerOwner');
    }

    private function mockForm($isValid = true)
    {
        $mockForm = $this->getMockBuilder(ContainerOwnerForm::class)->disableOriginalConstructor()->getMock();
        $mockForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(ContainerOwnerForm::class, $mockForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new ContainerOwner();
        $mockEntity->setName('testName');

        $mockService = $this->getMockBuilder(ContainerOwnerService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(ContainerOwnerService::class, $mockService);
    }
}
