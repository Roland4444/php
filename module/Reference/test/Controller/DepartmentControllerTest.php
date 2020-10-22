<?php

namespace ReferenceTest\Controller;

use ApplicationTest\Controller\AbstractControllerTest;
use Reference\Controller\DepartmentController;
use Reference\Entity\Department;
use Reference\Form\DepartmentForm;
use Reference\Service\DepartmentService;

class DepartmentControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/department', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockCategory = new Department();
        $this->mockService([$mockCategory, $mockCategory]);

        $this->dispatch('/reference/department', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/department/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/department/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/department/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/department');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/department/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/department/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/department/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/department');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/department/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/department');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    public function testListAction()
    {
        $this->mockService();
        $this->dispatch('/reference/department/list', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('list');
        $this->assertStandardParameters();
    }


    private function mockService($findAllReturn = [])
    {
        $mockedEntity = new Department();

        $mockCategoryService = $this->getMockBuilder(DepartmentService::class)->disableOriginalConstructor()->getMock();
        $mockCategoryService->expects($this->any())->method('find')->willReturn($mockedEntity);
        $mockCategoryService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(DepartmentService::class, $mockCategoryService);
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(DepartmentForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(DepartmentForm::class, $mockCategoryForm);
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(DepartmentController::class);
        $this->assertControllerClass('DepartmentController');
        $this->assertMatchedRouteName('department');
    }
}
