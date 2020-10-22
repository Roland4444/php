<?php
/**
 * Created by PhpStorm.
 * User: kostyrenko
 * Date: 05.02.2019
 * Time: 17:17
 */

namespace ModuleTest\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Exception\ServiceException;
use Application\Form\Filter\SubmitElement;
use Doctrine\ORM\EntityManager;
use Modules\Controller\MoveVehiclesController;
use Modules\Entity\MoveVehiclesEntity;
use Modules\Service\CompletedVehicleTripsService;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Role;
use Reference\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;

class MoveVehiclesControllerTest extends TestCase
{
    const LIMIT_MONTH = MoveVehiclesController::LIMIT_MONTH + 1;

    private EntityManager $entityManager;
    private Logger $logger;

    protected function setUp():void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(Logger::class);
    }
    /**
     * Проверка на получение данных для индексной страницы.
     */
    public function testGetTableListData()
    {
        $service = $this->createMock(CompletedVehicleTripsService::class);
        $service->expects($this->once())
            ->method('findBy')
            ->with(12)
            ->willReturn(true);

        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, $service, []);
        $this->assertTrue($instance->getTableListData(12));
    }

    /**
     * Проверка получения формы фильтра.
     */
    public function testGetFilterForm()
    {
        $mockCurrentUSer = $this->createMock(CurrentUser::class);
        $mockCurrentUSer->method('isAdmin')->willReturn(true);

        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $instance->setCurrentUser($mockCurrentUSer);

        $this->assertInstanceOf(SubmitElement::class, $instance->getFilterForm());
    }

    /**
     * Проверка получения информации о правах пользователя.
     */
    public function testGetPermissions()
    {
        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $result = $instance->getPermissions();

        $this->assertArrayHasKey('add', $result);
        $this->assertArrayHasKey('edit', $result);
        $this->assertArrayHasKey('delete', $result);
        $this->assertArrayHasKey('complete', $result);
    }

    /**
     * Проверка метода валидации прав пользователя на действия требующие прав и нормальной даты.
     *
     * @dataProvider providerAccessAction
     * @param $hasAccess
     * @param $date
     * @param $expected
     * @throws \Exception
     */
    public function testAccessValidate($hasAccess, $date, $expected)
    {
        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $instance->setHasAccess($hasAccess);

        $this->assertEquals($expected, $instance->accessValidate($date, MockMoveVehiclesController::LIMIT_MONTH));
    }

    /**
     * Проверка метода валидации прав пользователя на изменение требующие прав и нормальной даты.
     *
     * @dataProvider checkAccessToEdit
     * @param $expected
     * @throws \Exception
     */
    public function testCheckAccessToEdit($expected)
    {
        $entity = new MoveVehiclesEntity();
        $entity->setDate($this->getCurrentDate());

        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $instance->mockAccessValidate = $expected;

        $this->assertEquals($expected, $instance->checkAccessToEdit($entity));
    }

    /**
     * Проверка данных при изменении на выполнение пользователем разрешенных действий.
     *
     * @throws ServiceException
     */
    public function testCheckDataForEditHasAccess()
    {
        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $instance->mockAccessValidate = true;
        $params = ['date' => '2005-01-01'];

        $this->assertEquals($params, $instance->checkDataForEdit($params));
    }

    /**
     * Проверка данных при изменении на выполнение пользователем неразрешенного действия.
     *
     * @throws ServiceException
     */
    public function testCheckDataForEditNoHasAccess()
    {
        $instance = new MockMoveVehiclesController($this->entityManager, $this->logger, null, []);
        $instance->mockAccessValidate = false;
        $params = ['date' => '2005-01-01'];

        $this->expectException(ServiceException::class);
        $instance->checkDataForEdit($params);
    }

    /**
     * Тестовые данные для проверки действий пользователей с разными правами и датами.
     *
     * @return array
     */
    public function providerAccessAction()
    {
        return [
            //Пользователь с правами на удаление, нормальная дата
            [true, $this->getCurrentDate(), true],
            //Пользователь с правами на удаление, старая дата
            [true, $this->getOldDate(), true],
            //Пользователь без прав на удаление, старая дата
            [false, $this->getOldDate(), false],
            //Пользователь без прав на удаление, нормальная дата
            [false, $this->getCurrentDate(), true],
        ];
    }

    /**
     * Тестовые данные для проверки действий пользователей с разными правами и датами.
     *
     * @return array
     */
    public function checkAccessToEdit()
    {
        return [
            [true], [false]
        ];
    }

    /**
     * Создает дату в диапозоне лимита.
     *
     * @return false|string
     */
    protected function getCurrentDate()
    {
        return date('Y-m-d');
    }

    /**
     * Создает дату в диапозоне вне лимита.
     *
     * @return false|string
     */
    protected function getOldDate()
    {
        return (new \DateTime())->modify('-' . self::LIMIT_MONTH . ' month')->format('Y-m-d');
    }
}
