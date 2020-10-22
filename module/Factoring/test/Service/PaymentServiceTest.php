<?php

namespace FactoringTest\Service;

use Factoring\Repository\PaymentRepository;
use Factoring\Service\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(PaymentRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindByPeriod()
    {
        $this->repository->expects($this->once())
            ->method('findByPeriod')
            ->with('2000-01-01', '2019-01-01')
            ->willReturn([]);

        $service = new PaymentService($this->repository);
        $result = $service->findByPeriod('2000-01-01', '2019-01-01', null);

        $this->assertIsArray($result);
    }

    public function testGetSumFactoring()
    {
        $this->repository->expects($this->once())
            ->method('getSumFactoring')
            ->with('2000-01-01', 1)
            ->willReturn(15132);

        $service = new PaymentService($this->repository);
        $result = $service->getSumFactoring('2000-01-01', 1);

        $this->assertEquals(15132, $result);
    }
}
