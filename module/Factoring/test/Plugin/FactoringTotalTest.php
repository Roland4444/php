<?php

namespace FactoringTest\Plugin;

use Factoring\Entity\Provider;
use Factoring\Plugin\FactoringTotal;
use Factoring\Service\AssignmentDebtService;
use Factoring\Service\PaymentService;
use Factoring\Service\ProviderService;
use Factoring\Service\SalesService;
use PHPUnit\Framework\TestCase;

class FactoringTotalTest extends TestCase
{
    public function testInvoke()
    {
        $salesService = $this->getMockBuilder(SalesService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $salesService->method('getSumFactoring')->willReturn(40.0);

        $paymentService = $this->getMockBuilder(PaymentService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paymentService->method('getSumFactoring')->willReturn(20.0);

        $providerService = $this->getMockBuilder(ProviderService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $provider = new Provider();
        $provider->setTitle('Test title');
        $providerService->method('find')->willReturn($provider);

        $assignmentDebtService = $this->getMockBuilder(AssignmentDebtService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $assignmentDebtService->method('getSumFactoring')->willReturn(30.0);

        $plugin = new FactoringTotal($salesService, $paymentService, $providerService, $assignmentDebtService);
        $result = $plugin->__invoke(date('Y-m-d'));
        $this->assertIsArray($result);
        $this->assertEquals('Test title', $result['title']);
        $this->assertEquals(50.0, floatval($result['total']));
    }
}
