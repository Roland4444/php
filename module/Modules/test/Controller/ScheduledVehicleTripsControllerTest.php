<?php

namespace ModuleTest\Controller;

use Application\Exception\ServiceException;
use Doctrine\ORM\EntityManager;
use Exception;
use Modules\Controller\MoveVehiclesController;
use Modules\Form\MoveVehiclesCompleteForm;
use Modules\Form\MoveVehiclesForm;
use Modules\Service\CompletedVehicleTripsService;
use Modules\Service\ScheduledVehicleTripsService;
use Modules\Service\TransportIncasService;
use Modules\Controller\ScheduledVehicleTripsController;
use Modules\Entity\MoveVehiclesEntity;
use Reference\Entity\Vehicle;
use Reference\Service\VehicleService;
use Reports\Service\RemoteSkladService;
use Zend\Log\Logger;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ScheduledVehicleTripsControllerTest extends AbstractHttpControllerTestCase
{
    const LIMIT_MONTH = MoveVehiclesController::LIMIT_MONTH + 1;

    private EntityManager $entityManager;
    private Logger $logger;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(Logger::class);
    }

    /**
     * Тестируем конструктор
     */
    public function testConstructor()
    {
        $instance = new ScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $this->assertInstanceOf(ScheduledVehicleTripsController::class, $instance);
    }

    /**
     * Проверка получения формы создания.
     */
    public function testGetCreateForm()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $this->assertInstanceOf(MoveVehiclesForm::class, $instance->getCreateForm());
    }

    /**
     * Проверка получения сущности для создания выезда.
     *
     * @throws \ReflectionException
     */
    public function testGetEntityForCreate()
    {
        $mockVehicleService = $this->createMock(VehicleService::class);
        $mockVehicleService->expects($this->once())
            ->method('find')
            ->with($this->equalTo(123))
            ->willReturn(new Vehicle());
        $services = [VehicleService::class => $mockVehicleService];
        $instance = new ScheduledVehicleTripsController($this->entityManager, $this->logger, null, $services);

        $method = new \ReflectionMethod($instance, 'getEntityForCreate');
        $method->setAccessible(true);

        $params = [
            'vehicle' => 123,
        ];
        $entity = $method->invoke($instance, $params);

        $this->assertInstanceOf(MoveVehiclesEntity::class, $entity);
    }

    /**
     * Проверка получения формы редактирования.
     */
    public function testGetEditForm()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $this->assertInstanceOf(MoveVehiclesForm::class, $instance->getEditForm());
    }

    /**
     * Проверка получения формы завершения выезда
     */
    public function testGetCompleteForm()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $instance->setIsMoveVehiclesCompleteForm();
        $this->assertInstanceOf(MoveVehiclesCompleteForm::class, $instance->getEditForm());
    }

    /**
     * Проверка метода checkDataForCreate с разрешенным параметрами.
     *
     * @dataProvider providerHasAccess
     * @param $hasAccess
     * @param $date
     * @throws ServiceException
     */
    public function testCheckDataForCreateNormal($hasAccess, $date)
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $instance->setHasAccess($hasAccess);
        $params = [
            'date' => $date,
        ];
        $this->assertEquals($params, $instance->checkDataForCreate($params));
    }

    /**
     * Проверка данных при создании. Старая дата и пользователь без прав удаления.
     *
     * @throws Exception
     */
    public function testCheckDataForCreateWithException()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $instance->setHasAccess(false);
        $params = [
            'date' => $this->getOldDate(),
        ];
        $this->expectException(ServiceException::class);
        $instance->checkDataForCreate($params);
    }

    /**
     * Проверка данных при завершении выезда.
     *
     * @throws ServiceException
     */
    public function testCheckDataForCompleted()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $instance->setIsMoveVehiclesCompleteForm();
        $this->assertIsArray($instance->checkDataForEdit([]));
        $this->assertEquals('completedVehicleTrips', $instance->getIndexRout());
    }

    /**
     * Проверка метода завершения выезда.
     *
     * @throws Exception
     */
    public function testCompleteAction()
    {
        $incasService = $this->createMock(TransportIncasService::class);
        $remoteService = $this->createMock(RemoteSkladService::class);
        $services = [
            CompletedVehicleTripsService::class => new CompletedVehicleTripsService(null, $remoteService, $incasService),
        ];
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, $services);
        $this->assertEquals('EditAction', $instance->completeAction());
        $this->assertInstanceOf(CompletedVehicleTripsService::class, $instance->getService());
        $this->assertTrue($instance->getIsMoveVehiclesCompleteForm());
    }

    /**
     * Проверка сущности при завершении выезда. Запись с таким id не найдена.
     *
     */
    public function testGetEntityForEditEntityNotFound()
    {
        $service = $this->createMock(ScheduledVehicleTripsService::class);
        $service->expects($this->once())
            ->method('find')
            ->with(12)
            ->willReturn(null);

        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, $service, []);
        $returnEntity = $instance->getEntityForEdit(12);
        $this->assertNull($returnEntity);
    }

    /**
     * Проверка сущности при завершении выезда. isMoveVehiclesCompleteForm = false.
     *
     */
    public function testGetEntityForEditIsMoveVehiclesCompleteFormFalse()
    {
        $service = $this->createMock(ScheduledVehicleTripsService::class);
        $service->expects($this->once())
            ->method('find')
            ->with(12)
            ->willReturn(new MoveVehiclesEntity());

        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, $service, []);
        $returnEntity = $instance->getEntityForEdit(12);
        $this->assertInstanceOf(MoveVehiclesEntity::class, $returnEntity);
        $this->assertEquals(0, $returnEntity->getCompleted());

        $obj = new \ReflectionObject($returnEntity);
        $p = $obj->getProperty('isIncludeRequiredParams');
        $p->setAccessible(true);
        $this->assertEquals(true, $p->getValue($returnEntity));
    }

    /**
     * Проверка сущности при завершении выезда. isMoveVehiclesCompleteForm = true.
     *
     */
    public function testGetEntityForEditIsMoveVehiclesCompleteFormTrue()
    {
        $service = $this->createMock(ScheduledVehicleTripsService::class);
        $service->expects($this->once())
            ->method('find')
            ->with(12)
            ->willReturn(new MoveVehiclesEntity());

        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, $service, []);
        $instance->setIsMoveVehiclesCompleteForm();
        $returnEntity = $instance->getEntityForEdit(12);
        $this->assertInstanceOf(MoveVehiclesEntity::class, $returnEntity);
        $this->assertEquals(1, $returnEntity->getCompleted());

        $obj = new \ReflectionObject($returnEntity);
        $p = $obj->getProperty('isIncludeRequiredParams');
        $p->setAccessible(true);
        $this->assertEquals(false, $p->getValue($returnEntity));
    }

    /**
     * Проверка данных при редактировании выезда. Разрешенные действия.
     *
     * @dataProvider providerHasAccess
     * @param $hasAccess
     * @param $date
     * @throws ServiceException
     */
    public function testCheckDataForEditNormal($hasAccess, $date)
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $instance->setHasAccess($hasAccess);
        $params = ['date' => $date];
        $this->assertEquals($params, $instance->checkDataForEdit($params));
    }

    /**
     * Проверка данных при редактировании выезда. Старая дата и пользователь без прав удаления.
     *
     * @throws ServiceException
     */
    public function testCheckDataForEditWithException()
    {
        $instance = new MockScheduledVehicleTripsController($this->entityManager, $this->logger, null, []);
        $params = ['date' => $this->getOldDate()];
        $instance->setHasAccess(false);
        $this->expectException(ServiceException::class);
        $instance->checkDataForEdit($params);
    }

    /**
     * Создает текущию дату
     *
     * @return false|string
     */
    protected function getCurrentDate()
    {
        return date('Y-m-d');
    }

    /**
     * Создает дату в диапозоне вне лимита
     *
     * @return false|string
     * @throws Exception
     */
    protected function getOldDate()
    {
        return (new \DateTime())->modify('-' . self::LIMIT_MONTH . ' month')->format('Y-m-d');
    }

    /**
     * Данные для проверки разрешенных действий.
     *
     * @return array
     * @throws Exception
     */
    public function providerHasAccess()
    {
        return [
            //Пользователь с правами на удаление, нормальная дата
            [true, $this->getCurrentDate()],
            //Пользователь с правами на удаление, старая дата
            [true, $this->getOldDate()],
            //Пользователь без прав на удаление, нормальная дата
            [false, $this->getCurrentDate()],
        ];
    }
}
