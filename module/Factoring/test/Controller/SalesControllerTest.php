<?php

namespace FactoringTest\Controller;

use Factoring\Service\SalesService;
use PHPUnit\Framework\TestCase;

class SalesControllerTest extends TestCase
{
    public function testIndexAction()
    {
        $service = $this->createMock(SalesService::class);
        $shipments = [
            [
                'sum' => 153
            ],
            [
                'sum' => 123
            ]
        ];

        $service->expects($this->once())
            ->method('findByPeriod')
            ->with(date('Y-m-01'), date('Y-m-t'))
            ->willReturn($shipments);

        $controller = new MockSalesController($service);
        $viewModel = $controller->indexAction();

        $this->assertIsArray($viewModel->getVariables());
        $this->assertEquals(276, $viewModel->getVariable('data')['sum']);
    }
}
