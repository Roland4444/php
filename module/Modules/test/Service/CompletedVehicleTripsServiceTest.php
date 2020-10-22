<?php

namespace ModuleTest\Service;

use Application\Exception\ServiceException;
use Modules\Entity\MoveVehiclesEntity;
use Modules\Service\CompletedVehicleTripsService;
use Modules\Service\TransportIncasService;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;
use Reference\Entity\Department;
use Reports\Entity\RemoteSklad;
use Reports\Service\RemoteSkladService;
use \Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

class CompletedVehicleTripsServiceTest extends TestCase
{
    protected $mockEntityManager;

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
        $mockEntityManager->method('createQueryBuilder')->willReturn(new MockQueryBuilder($entityManager));
        $this->mockEntityManager = $mockEntityManager;
    }

    /**
     * Проверка генерируемого запроса по указанным параметрам метода getTotalSumByDepartment.
     *
     * @dataProvider providerGetTotalSumByDepartment
     * @param $params
     * @param $expectedResult
     */
    public function testGetTotalSumByDepartment($params, $expectedResult)
    {
        $dateFrom = $params['dateFrom'];
        $dateTo = $params['dateTo'];
        $departmentId = $params['departmentId'] ?? null;
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);
        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $this->assertEquals($expectedResult, $service->getTotalSumByDepartment($dateFrom, $dateTo, $departmentId));
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
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $this->assertEquals($expectedResult, $service->findBy($params));
    }

    public function testFindTripsForWaybillWithCorrectParams()
    {
        $params = [
            'startDate' => '2012-01-01',
            'endDate' => '2012-12-12',
            'driver' => '1',
            'vehicle' => '1',

        ];
        $expectedResult = "SELECT r FROM \Modules\Entity\MoveVehiclesEntity r WHERE r.completed = 1 AND  r.date >= '2012-01-01'  AND  r.date <= '2012-12-12'  AND  r.driver = '1'  AND  r.vehicle = '1'  AND  r.distance <> 0  AND  r.distance IS NOT NULL  ORDER BY r.date ASC, r.departureTime ASC";
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);


        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $this->assertEquals($expectedResult, $service->findTripsForWaybill($params));
    }

    public function testFindTripsForWaybillWithEmptyVehicle()
    {
        $this->expectException(ServiceException::class);
        $params = [
            'startDate' => '2012-01-01',
            'endDate' => '2012-12-12',
            'driver' => '1',
        ];
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $service->findTripsForWaybill($params);
    }

    public function testFindTripsForWaybillWithEmptyDriver()
    {
        $this->expectException(ServiceException::class);
        $params = [
            'startDate' => '2012-01-01',
            'endDate' => '2012-12-12',
            'vehicle' => '2'
        ];
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $service->findTripsForWaybill($params);
    }

    public function testFindTripsForWaybillWithEmptyDates()
    {
        $this->expectException(ServiceException::class);
        $params = [
            'driver' => '1',
            'vehicle' => '2'
        ];
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $service->findTripsForWaybill($params);
    }

    /**
     * Проверка сохранения при условии переданого пустого номера накладной.
     *
     */
    public function testSaveWithEmptyWaybill()
    {
        $row = $this->getMoveVehicleEntity();

        $params = new Parameters(['waybill' => '']);
        $request = new Request();
        $request->setPost($params);
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $remoteService, $incasService);
        $result = $service->save($row, $request);

        $this->assertTrue($result);
        $this->assertNull($row->getRemoteSklad());
    }

    public function testSaveWithWrongWaybill()
    {
        $row = $this->getMoveVehicleEntity();

        $params = new Parameters(['waybill' => '1']);
        $request = new Request();
        $request->setPost($params);

        $mockRemoteSkladService = $this->getMockBuilder(RemoteSkladService::class)
            ->setMethods(['findWaybillInExport'])
            ->getMock();
        $mockRemoteSkladService->method('findWaybillInExport')->willReturn(null);
        $incasService = $this->createMock(TransportIncasService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $mockRemoteSkladService, $incasService);
        $this->expectException(ServiceException::class);
        $service->save($row, $request);
    }

    public function testSaveWithFoundWaybill()
    {
        $row = $this->getMoveVehicleEntity();

        $params = new Parameters(['waybill' => '1']);
        $request = new Request();
        $request->setPost($params);

        $mockRemoteSkladService = $this->getMockBuilder(RemoteSkladService::class)
            ->setMethods(['findWaybillInExport'])
            ->getMock();
        $remoteSklad = new RemoteSklad();
        $remoteSklad->setId(125);
        $mockRemoteSkladService->method('findWaybillInExport')->willReturn($remoteSklad);
        $incasService = $this->createMock(TransportIncasService::class);

        $service = new CompletedVehicleTripsService($this->mockEntityManager, $mockRemoteSkladService, $incasService);
        $result = $service->save($row, $request);
        $this->assertTrue($result);
        $this->assertEquals(125, $row->getRemoteSklad()->getId());
    }

    /**
     * Возвращает фейковую сущность с данными.
     *
     * @return MoveVehiclesEntity
     */
    protected function getMoveVehicleEntity()
    {
        $department = new Department();
        $department->setName('testDepartment');

        $row = new MoveVehiclesEntity();
        $row->setDate('12-12-2012');
        $row->setDepartment($department);
        return $row;
    }

    /**
     * Возвращает фейковый запрос.
     *
     * @return Request
     */
    protected function getRequest()
    {
        $params = new Parameters(['waybill' => 1]);
        $request = new Request();
        $request->setPost($params);

        return $request;
    }

    /**
     * Тестовые данные для testGetTotalSumByDepartment.
     *
     * @return array
     */
    public function providerGetTotalSumByDepartment()
    {
        return [
            [
                [
                    'dateFrom' => '2012-01-12',
                    'dateTo' => '2012-12-12',
                    'departmentId' => 1
                ],
                "SELECT SUM(r.payment) FROM \Modules\Entity\MoveVehiclesEntity r WHERE r.completed = 1 AND (r.date >='2012-01-12' and r.date<='2012-12-12') AND r.moneyDepartment = 1"
            ],
            [
                [
                    'dateFrom' => '2222-01-12',
                    'dateTo' => '2222-12-12'
                ],
                "SELECT SUM(r.payment) FROM \Modules\Entity\MoveVehiclesEntity r WHERE r.completed = 1 AND (r.date >='2222-01-12' and r.date<='2222-12-12')"
            ],

        ];
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
                [
                    'startdate' => '2012-01-01',
                    'enddate' => '2012-12-12',
                    'customerText' => '1',
                    'vehicle' => '2',
                    'department' => '3',

                ],
                "SELECT m, r FROM \Modules\Entity\MoveVehiclesEntity m LEFT JOIN m.remoteSklad r WHERE m.completed = 1 AND  m.date >= '2012-01-01'  AND  m.date <= '2012-12-12'  AND m.customer like '%1%' AND m.vehicle = 2 AND m.department = 3 ORDER BY m.date DESC"
            ],
            [
                [
                    'startdate' => '2012-01-01',
                    'enddate' => '2222-12-12',
                    'vehicle' => '20',
                    'department' => '30',
                ],
                "SELECT m, r FROM \Modules\Entity\MoveVehiclesEntity m LEFT JOIN m.remoteSklad r WHERE m.completed = 1 AND  m.date >= '2012-01-01'  AND  m.date <= '2222-12-12'  AND m.vehicle = 20 AND m.department = 30 ORDER BY m.date DESC"
            ],
            [
                [
                    'vehicle' => '20',
                    'department' => '30',
                ],
                "SELECT m, r FROM \Modules\Entity\MoveVehiclesEntity m LEFT JOIN m.remoteSklad r WHERE m.completed = 1 AND  m.date >= '" . date('Y-m-01') . "'  AND  m.date <= '" . date('Y-m-t') . "'  AND m.vehicle = 20 AND m.department = 30 ORDER BY m.date DESC"
            ]
        ];
    }
}
