<?php

namespace ReferenceTest\functional\Controller;

use ApplicationTest\Controller\AbstractControllerTest;
use Reference\Controller\EmployeeController;
use Reference\Entity\Employee;
use Reference\Form\EmployeeForm;
use Reference\Service\EmployeeService;

class EmployeeControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();
        $this->dispatch('/reference/employee', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockCategory = new Employee();
        $this->mockService([$mockCategory, $mockCategory]);

        $this->dispatch('/reference/employee', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/employee/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/employee/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/employee/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/employee');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/employee/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/employee/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/employee/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/employee');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/employee/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/employee');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(EmployeeForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(EmployeeForm::class, $mockCategoryForm);
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(EmployeeController::class);
        $this->assertControllerClass('EmployeeController');
        $this->assertMatchedRouteName('employee');
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new Employee();

        $mockService = $this->getMockBuilder(EmployeeService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(EmployeeService::class, $mockService);
    }
}
