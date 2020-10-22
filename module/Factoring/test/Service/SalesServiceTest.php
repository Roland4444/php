<?php

namespace FactoringTest\Service;

use Factoring\Service\SalesService;
use PHPUnit\Framework\TestCase;
use Storage\Facade\Storage;

class SalesServiceTest extends TestCase
{
    private $storageFacade;

    protected function setUp(): void
    {
        $this->storageFacade = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindByPeriod()
    {
        $this->storageFacade->expects($this->once())
            ->method('findShipmentsWithFactoring')
            ->with('2000-01-01', '2019-01-01')
            ->willReturn([]);

        $service = new SalesService($this->storageFacade);
        $result = $service->findByPeriod('2000-01-01', '2019-01-01');

        $this->assertIsArray($result);
    }

    public function testGetSumFactoring()
    {
        $this->storageFacade->expects($this->once())
            ->method('getSumShipmentWithFactoring')
            ->with('2000-01-01')
            ->willReturn(15132.0);

        $service = new SalesService($this->storageFacade);
        $result = $service->getSumFactoring('2000-01-01');

        $this->assertEquals(15132, $result);
    }
}
