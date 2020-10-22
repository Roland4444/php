<?php

namespace FactoringTest\Controller;

use Factoring\Entity\Provider;
use Factoring\Service\AssignmentDebtService;
use Factoring\Service\PaymentService;
use Factoring\Service\ProviderService;
use Factoring\Service\SalesService;
use PHPUnit\Framework\TestCase;

class TotalControllerTest extends TestCase
{
    public function testIndexAction()
    {
        $salesService = $this->createMock(SalesService::class);
        $salesService->method('getSumFactoring')->willReturn(500.0);

        $paymentService = $this->createMock(PaymentService::class);
        $paymentService->method('getSumFactoring')->willReturn(300.0);

        $providerService = $this->createMock(ProviderService::class);
        $providerService->method('find')->willReturn(new Provider());

        $assignmentDebtService = $this->createMock(AssignmentDebtService::class);
        $assignmentDebtService->method('getSumFactoring')->willReturn(150.0);

        $controller = new MockTotalController($salesService, $paymentService, $providerService, $assignmentDebtService);
        $viewModel = $controller->indexAction();

        $this->assertIsArray($viewModel->getVariables());
        $this->assertEquals(350, $viewModel->getVariable('data')['sum']);
    }
}
