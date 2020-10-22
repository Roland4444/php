<?php

namespace FactoringTest\Service;

use Factoring\Repository\AssignmentDebtRepository;
use Factoring\Service\AssignmentDebtService;
use PHPUnit\Framework\TestCase;

class AssignmentDebtServiceTest extends TestCase
{
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(AssignmentDebtRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindByPeriod()
    {
        $this->repository->expects($this->once())
            ->method('findByPeriod')
            ->with('2000-01-01', '2019-01-01')
            ->willReturn([]);

        $service = new AssignmentDebtService($this->repository);
        $result = $service->findByPeriod('2000-01-01', '2019-01-01');

        $this->assertIsArray($result);
    }

    public function testGetSumFactoring()
    {
        $this->repository->expects($this->once())
            ->method('getSumFactoring')
            ->with('2000-01-01', 1)
            ->willReturn(15132);

        $service = new AssignmentDebtService($this->repository);
        $result = $service->getSumFactoring('2000-01-01', 1);

        $this->assertEquals(15132, $result);
    }

    public function testGetSumGroupByTrader()
    {
        $this->repository->expects($this->once())
            ->method('getSumGroupByTrader')
            ->willReturn([[
                'id' => '123',
                'sum' => '5555'
            ]]);

        $service = new AssignmentDebtService($this->repository);
        $result = $service->getSumGroupByTrader('2018-05-05');

        $this->assertIsArray($result);
        $this->assertEquals('5555', $result[123]);
    }
}
