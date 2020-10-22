<?php

namespace ProjectTest\Controller;

use Finance\Form\BankForm;
use Finance\Service\BankService;
use ProjectTest\Bootstrap;
use Finance\Controller\BankController;
use PHPUnit\Framework\TestCase;
use Zend\View\Model\ViewModel;

class BankControllerTest extends TestCase
{
    public function testConstructor()
    {
        $mockService = $this->createMock(BankService::class);
        $mockForm = $this->createMock(BankForm::class);

        $this->assertInstanceOf(BankController::class, new BankController($mockService, $mockForm));
    }

    public function testIndexAction()
    {
        $serviceLocator = Bootstrap::getServiceManager();
        $serviceLocator->setAllowOverride(true);

        $mockService = $this->createMock(BankService::class);
        $mockService->expects($this->once())
            ->method('findAll')
            ->with($this->equalTo(true));

        $serviceLocator->setService(BankService::class, $mockService);
        $serviceLocator->setAllowOverride(false);

        $mockForm = $this->createMock(BankForm::class);

        $controller = new BankController($mockService, $mockForm);
        $viewModel = $controller->indexAction();

        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertTrue(is_array($viewModel->rows));
    }
}
