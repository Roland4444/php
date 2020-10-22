<?php

namespace FinanceTest\functional\Controller;

use ApplicationTest\Controller\AbstractControllerTest;
use Finance\Controller\BankController;
use Finance\Entity\BankAccount;
use Finance\Form\BankForm;
use Finance\Service\BankService;

class BankControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();
        $this->dispatch('/reference/bank', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('finance');
        $this->assertControllerName(BankController::class);
        $this->assertControllerClass('BankController');
        $this->assertMatchedRouteName('bank');
    }

    public function testIndexActionWithRows()
    {
        $mockCategory = new BankAccount();
        $this->mockService([$mockCategory, $mockCategory]);

        $this->dispatch('/reference/bank', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/bank/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/bank/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/bank/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/bank');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/bank/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/bank/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/bank/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/bank');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/bank/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/bank');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(BankForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(BankForm::class, $mockCategoryForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new BankAccount();

        $mockService = $this->getMockBuilder(BankService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(BankService::class, $mockService);
    }
}
