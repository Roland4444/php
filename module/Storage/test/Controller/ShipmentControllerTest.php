<?php

namespace StorageTest\Controller;

use Core\Service\AccessValidateService;
use Core\Utils\Options;
use Doctrine\ORM\EntityManager;
use LogicException;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Department;
use Reference\Entity\Metal;
use Reference\Entity\User;
use Reference\Service\ShipmentTariffService;
use Reference\Service\TraderService;
use Storage\Controller\ShipmentController;
use Storage\Entity\Container;
use Storage\Entity\ContainerItem;
use Storage\Entity\Shipment;
use Storage\Form\ShipmentForm;
use Storage\Service\ShipmentService;
use StorageTest\Controller\Mock\MockShipmentController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ShipmentControllerTest extends TestCase
{
    private ShipmentController $controller;
    private ShipmentService $shipmentService;
    private AccessValidateService $accessValidateService;
    private TraderService $traderService;
    private ShipmentTariffService $tariffService;
    private ShipmentForm $form;


    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->shipmentService = $this->createMock(ShipmentService::class);
        $this->accessValidateService = $this->createMock(AccessValidateService::class);
        $this->traderService = $this->createMock(TraderService::class);
        $this->tariffService = $this->createMock(ShipmentTariffService::class);
        $this->form = $this->createMock(ShipmentForm::class);
        $this->controller = new MockShipmentController(
            $this->entityManager,
            $this->shipmentService,
            $this->accessValidateService,
            $this->traderService,
            $this->tariffService,
            $this->form
        );
    }

    /**
     * Если currentDepartment == null должно быть брошено исключение
     */
    public function testIndexActionDepartmentNullExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current department can\'t be null');

        $this->controller->setCurrentDepartment(null);

        $this->controller->indexAction();
    }

    /**
     * Если подразделение скрыто должно быть брошено исключение 'Department not found'
     */
    public function testIndexActionHideDepartmentExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Department not found');

        $department = new Department();
        $department->setOption(Options::HIDE, true);
        $this->controller->setCurrentDepartment($department);

        $this->controller->indexAction();
    }

    public function testIndexActionRedirectExpected(): void
    {
        $this->controller->setRouteParam('department', 111);
        $this->controller->setFilterParam('department', 222);
        $user = new User();
        $user->setDepartment(null);
        $this->controller->setCurrentUser($user);

        $result = $this->controller->indexAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::STATUS_CODE_301, $result->getStatusCode());
    }

    public function testIndexActionSuccessExpected(): void
    {
        $this->controller->setRouteParam('department', 0);
        $this->controller->setFilterParam('department', 0);
        $this->controller->setFilterParam('startdate', '');
        $this->controller->setFilterParam('enddate', '');
        $this->controller->setFilterParam('trader', null);

        $result = $this->controller->indexAction();

        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testAddActionExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current department can\'t be null');

        $this->controller->setCurrentDepartment(null);

        $this->controller->addAction();
    }

    public function testAddActionSuccessExpected(): void
    {
        $result = $this->controller->addAction();

        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testAddShipmentActionExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current department can\'t be null');

        $this->controller->setCurrentDepartment(null);

        $this->controller->addShipmentAction();
    }

    public function testAddShipmentActionShipmentIdExists(): void
    {
        $this->controller->setRouteParam('id', 1);
        $department = new Department();
        $department->setType(Department::TYPE_BLACK);
        $this->controller->setCurrentDepartment($department);

        $result = $this->controller->addShipmentAction();

        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testAddShipmentActionShipmentIdNotExists(): void
    {
        $department = new Department();
        $department->setType(Department::TYPE_BLACK);
        $this->controller->setCurrentDepartment($department);

        $result = $this->controller->addShipmentAction();

        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testSaveAjaxActionMethodNotAllowedError(): void
    {
        $result = $this->controller->saveAjaxAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::STATUS_CODE_400, $result->getStatusCode());
    }

    public function testSaveAjaxActionCurrentDepartmentNullExceptionError(): void
    {
        $this->controller->setPost(true);
        $this->controller->setCurrentDepartment(null);

        $result = $this->controller->saveAjaxAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::STATUS_CODE_400, $result->getStatusCode());
    }

    public function testSaveAjaxActionFormInvalidError(): void
    {
        $this->controller->setPost(true, [
            'date' => date('Y-m-d')
        ]);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $result = $this->controller->saveAjaxAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::STATUS_CODE_400, $result->getStatusCode());
    }

    public function testSaveAjaxActionSuccessExpected(): void
    {
        $this->controller->setPost(true, [
            'date' => date('Y-m-d')
        ]);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $result = $this->controller->saveAjaxAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::STATUS_CODE_200, $result->getStatusCode());
    }



    public function testGetActionSuccessExpected(): void
    {
        $this->shipmentService
            ->expects($this->once())
            ->method('findByItemId')
            ->willReturn(new ContainerItem());

        $result = $this->controller->getItemAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testGetActionErrorExpected(): void
    {
        $this->shipmentService
            ->expects($this->once())
            ->method('findByItemId')
            ->willReturn(null);

        $result = $this->controller->getItemAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(400, $result->getStatusCode());
    }
}
