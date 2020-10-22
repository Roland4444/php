<?php

namespace ApplicationTest\Controller;

use Reference\Controller\CategoryController;
use Reference\Entity\Category;
use Reference\Entity\CategoryGroup;
use Reference\Entity\Role;
use Reference\Form\CategoryForm;
use Reference\Service\CategoryService;

class CategoryControllerTest extends AbstractControllerTest
{
    public function testIndexAction()
    {
        $this->mockService();

        $this->dispatch('/reference/category', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        $mockCategory = new Category();
        $mockCategory->setRoles([new Role()]);
        $mockCategory->setGroup(new CategoryGroup());
        $this->mockService([$mockCategory, $mockCategory]);

        $this->dispatch('/reference/category', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        $this->dispatch('/reference/category/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        $this->dispatch('/reference/category/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/category/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category');
        $this->assertActionName('add');
        $this->assertStandardParameters();
    }

    public function testEditActionGet()
    {
        $this->mockService();
        $this->dispatch('/reference/category/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormInvalid()
    {
        $this->mockService();
        $this->dispatch('/reference/category/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
    }

    public function testEditActionPostFormValid()
    {
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/category/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
    }

    public function testDeleteAction()
    {
        $this->mockService();
        $this->dispatch('/reference/category/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/category');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(CategoryController::class);
        $this->assertControllerClass('CategoryController');
        $this->assertMatchedRouteName('category');
    }

    private function mockForm($isValid = true)
    {
        $mockCategoryForm = $this->getMockBuilder(CategoryForm::class)->disableOriginalConstructor()->getMock();
        $mockCategoryForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(CategoryForm::class, $mockCategoryForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockCategory = new Category();
        $mockCategory->setName('testName');
        $mockCategory->setOption('default', true);

        $mockCategoryService = $this->getMockBuilder(CategoryService::class)->disableOriginalConstructor()->getMock();
        $mockCategoryService->expects($this->any())->method('find')->willReturn($mockCategory);
        $mockCategoryService->expects($this->any())->method('findAll')->willReturn($findAllReturn);

        $this->overrideService(CategoryService::class, $mockCategoryService);
    }
}
