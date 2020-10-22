<?php

namespace FactoringTest\Facade;

use Factoring\Entity\Provider;
use Factoring\Facade\Factoring;
use Factoring\Service\AssignmentDebtService;
use Factoring\Service\PaymentService;
use Factoring\Service\ProviderService;
use PHPUnit\Framework\TestCase;

class FactoringTest extends TestCase
{
    /**
     * @var Factoring
     */
    private $factoring;

    public function setUp(): void
    {
        $paymentService = $this->getMockBuilder(PaymentService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $assignmentDebtService = $this->getMockBuilder(AssignmentDebtService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $assignmentDebtService->method('getSumGroupByTrader')->willReturn([]);

        $providerService = $this->getMockBuilder(ProviderService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $providerService->method('getReference')->willReturn(new Provider());

        $this->factoring = new Factoring($paymentService, $assignmentDebtService, $providerService);
    }

    public function testFetAssignmentDebtGroupByTrader()
    {
        $result = $this->factoring->getAssignmentDebtGroupByTrader('2019-05-01');
        $this->assertIsArray($result);
    }
}
