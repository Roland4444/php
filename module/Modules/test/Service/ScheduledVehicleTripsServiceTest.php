<?php

namespace ModuleTest\Service;

use Modules\Service\ScheduledVehicleTripsService;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;

class ScheduledVehicleTripsServiceTest extends TestCase
{
    /**
     * @var ScheduledVehicleTripsService
     */
    protected $service;

    public function setUp(): void
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');

        $mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['flush','persist', 'createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockEntityManager->method('flush')->willReturn(true);
        $mockEntityManager->method('persist')->willReturn(true);
        $mockEntityManager->method('createQueryBuilder')
            ->willReturn(new MockQueryBuilder($entityManager));

        $this->service = new ScheduledVehicleTripsService($mockEntityManager);
    }

    /**
     * Проверка генерируемого запроса по указанным параметрам метода findBy.
     *
     * @dataProvider providerFindBy
     * @param $params
     * @param $expectedResult
     */
    public function testFindBy($params, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->service->findBy($params));
    }

    /**
     * Тестовые данные для testFindBy.
     *
     * @return array
     */
    public function providerFindBy()
    {
        return [
            [
              [],
              "SELECT r FROM \Modules\Entity\MoveVehiclesEntity r WHERE (r.completed = 0 OR r.completed is null) AND  r.date >= '" . date('Y-m-01') . "'  AND  r.date <= '" . date('Y-m-t') . "'  ORDER BY r.date DESC"
            ],
            [
                [
                    'startdate' => '2012-01-01',
                    'enddate' => '2012-12-12',
                    'customerText' => '1',
                    'vehicle' => '2',
                    'department' => '3',

                ],
                "SELECT r FROM \Modules\Entity\MoveVehiclesEntity r WHERE (r.completed = 0 OR r.completed is null) AND  r.date >= '2012-01-01'  AND  r.date <= '2012-12-12'  AND r.customer like '%1%' AND r.vehicle = 2 AND r.department = 3 ORDER BY r.date DESC"
            ],
            [
                [
                    'startdate' => '2012-01-01',
                    'enddate' => '2222-12-12',
                    'vehicle' => '2',
                    'department' => '3',

                ],
                "SELECT r FROM \Modules\Entity\MoveVehiclesEntity r WHERE (r.completed = 0 OR r.completed is null) AND  r.date >= '2012-01-01'  AND  r.date <= '2222-12-12'  AND r.vehicle = 2 AND r.department = 3 ORDER BY r.date DESC"
            ],
        ];
    }
}
