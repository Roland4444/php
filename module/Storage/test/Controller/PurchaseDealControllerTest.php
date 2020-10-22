<?php

namespace StorageTest\Controller;

use PHPUnit\Framework\TestCase;

use Storage\Controller\PurchaseDealController;
use Storage\Entity\Purchase;
use Storage\Entity\PurchaseDeal;
use Storage\Form\PurchaseDealForm;
use Storage\Service\PurchaseDealService;
use Storage\Service\PurchaseService;
use StorageTest\Controller\Mock\MockPurchaseDealController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class PurchaseDealControllerTest extends TestCase
{
    private PurchaseDealController $controller;
    private PurchaseService $purchaseService;
    private PurchaseDealService $purchaseDealService;
    private PurchaseDealForm $form;

    protected function setUp(): void
    {
        $this->purchaseService = $this->createMock(PurchaseService::class);
        $this->purchaseDealService = $this->createMock(PurchaseDealService::class);
        $this->form = $this->createMock(PurchaseDealForm::class);
        $this->controller = new MockPurchaseDealController($this->purchaseDealService, $this->purchaseService, $this->form);
    }

    public function testCheckAction(): void
    {
        $purchase = new Purchase();
        $this->purchaseService
            ->expects($this->once())
            ->method('getByDeal')
            ->willReturn([$purchase]);

        $result = $this->controller->checkAction();

        $this->assertTrue($result->terminate());
        $this->assertArrayHasKey('deal', $result->getVariables());
        $this->assertArrayHasKey('date', $result->getVariables());
        $this->assertArrayHasKey('checkData', $result->getVariables());
    }

    public function testEditActionGet(): void
    {
        $result = $this->controller->editAction();

        $this->assertInstanceOf(ViewModel::class, $result);
        $this->assertArrayHasKey('title', $result->getVariables());
        $this->assertArrayHasKey('form', $result->getVariables());
        $this->assertArrayHasKey('id', $result->getVariables());
        $this->assertArrayHasKey('departmentId', $result->getVariables());
        $this->assertArrayHasKey('indexRoute', $result->getVariables());
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testEditActionPost(): void
    {
        $deal = new PurchaseDeal();
        $this->purchaseDealService
            ->expects($this->once())
            ->method('find')
            ->willReturn($deal);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->controller->setPost(true);

        $result = $this->controller->editAction();

        $this->assertInstanceOf(Response::class, $result);
    }
}
