<?php

namespace ApplicationTest\Controller;

use Reference\Controller\MetalGroupController;
use Reference\Entity\MetalGroup;
use Reference\Form\MetalGroupForm;
use Reference\Service\MetalGroupService;

class MetalGroupControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/metal-group', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockEntity = new MetalGroup();
        $this->mockService([$mockEntity, $mockEntity]);

        $this->dispatch('/reference/metal-group', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/metal-group/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/metal-group/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/metal-group/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/metal-group');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/metal-group/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/metal-group/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/metal-group/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/metal-group');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/metal-group/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/metal-group');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(MetalGroupController::class);
        $this->assertControllerClass('MetalGroupController');
        $this->assertMatchedRouteName('metalGroup');
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(MetalGroupForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(MetalGroupForm::class, $mockCategoryForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new MetalGroup();
        $mockEntity->setName('Чермет');

        $mockService = $this->getMockBuilder(MetalGroupService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(MetalGroupService::class, $mockService);
    }
}
