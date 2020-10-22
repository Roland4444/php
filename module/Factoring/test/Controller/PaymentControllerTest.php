<?php

namespace FactoringTest\Controller;

use Doctrine\ORM\EntityManager;
use Factoring\Entity\Payment;
use Factoring\Form\PaymentForm;
use Factoring\Service\PaymentService;
use Finance\Entity\BankAccount;
use Finance\Service\BankService;
use PHPUnit\Framework\TestCase;
use Storage\Plugin\CurrentDepartment;
use Zend\Log\Logger;

class PaymentControllerTest extends TestCase
{
    private EntityManager $entityManager;
    private Logger $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(Logger::class);
    }

    public function testIndexAction()
    {
        $service = $this->createMock(PaymentService::class);

        $mockCurrentDepartment = $this->createMock(CurrentDepartment::class);
        $mockCurrentDepartment->expects($this->once())
            ->method('isHide')
            ->willReturn(false);

        $item = new Payment();
        $item->setMoney(100);
        $service->expects($this->once())
            ->method('findByPeriod')
            ->with(date('Y-m-01'), date('Y-m-t'))
            ->willReturn([$item]);

        $controller = new MockPaymentController($this->entityManager, $this->logger, $service, []);
        $controller->setMockCurrentDepartment($mockCurrentDepartment);
        $viewModel = $controller->indexAction();

        $this->assertIsArray($viewModel->getVariables());
    }

    public function testAddAction()
    {
        $service = $this->createMock(PaymentService::class);

        $bankService = $this->createMock(BankService::class);
        $bankService->method('findDefault')->willReturn(new BankAccount());

        $form = $this->createMock(PaymentForm::class);

        $services = [
            'bankService' => $bankService,
            'form' => $form
        ];

        $controller = new MockPaymentController($this->entityManager, $this->logger, $service, $services);
        $controller->initRequest('post');
        $viewModel = $controller->addAction();

        $this->assertIsArray($viewModel->getVariables());
    }

    public function testEditAction()
    {
        $service = $this->createMock(PaymentService::class);

        $form = $this->createMock(PaymentForm::class);

        $services = [
            'form' => $form
        ];

        $controller = new MockPaymentController($this->entityManager, $this->logger, $service, $services);
        $controller->initRequest('post');
        $viewModel = $controller->editAction();

        $this->assertIsArray($viewModel->getVariables());
    }
}
