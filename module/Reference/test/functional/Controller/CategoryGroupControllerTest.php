<?php

namespace ApplicationTest\Controller;

use Reference\Controller\CategoryGroupController;
use Reference\Entity\CategoryGroup;
use Reference\Form\CategoryGroupForm;
use Reference\Service\CategoryGroupService;

class CategoryGroupControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/category-group', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockEntity = new CategoryGroup();
        $this->mockService([$mockEntity, $mockEntity]);

        $this->dispatch('/reference/category-group', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/category-group/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/category-group/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/category-group/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category-group');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/category-group/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/category-group/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/category-group/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category-group');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/category-group/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category-group');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(CategoryGroupController::class);
        $this->assertControllerClass('CategoryGroupController');
        $this->assertMatchedRouteName('categoryGroup');
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(CategoryGroupForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(CategoryGroupForm::class, $mockCategoryForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new CategoryGroup();
        $mockEntity->setName('Прочие');

        $mockService = $this->getMockBuilder(CategoryGroupService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(CategoryGroupService::class, $mockService);
    }
}
