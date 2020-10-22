<?php

namespace StorageTest\Controller;

use Core\Service\AccessValidateService;
use LogicException;
use PHPUnit\Framework\TestCase;
use Storage\Controller\ContainerItemController;
use Storage\Entity\Container;
use Storage\Entity\ContainerItem;
use Storage\Entity\Shipment;
use Storage\Form\ContainerItemForm;
use Storage\Service\ContainerItemService;
use StorageTest\Controller\Mock\MockContainerItemController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ContainerItemControllerTest extends TestCase
{
    private ContainerItemController $controller;
    private ContainerItemService $service;
    private ContainerItemForm $form;
    private AccessValidateService $accessValidateService;

    public function setUp(): void
    {
        $this->service = $this->createMock(ContainerItemService::class);
        $this->form = $this->createMock(ContainerItemForm::class);
        $this->accessValidateService = $this->createMock(AccessValidateService::class);
        $this->controller = new MockContainerItemController($this->service, $this->form, $this->accessValidateService);
    }

    public function testAddAction(): void
    {
        $result = $this->controller->addAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testAddActionExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current department can\'t be null');

        $this->controller->setCurrentDepartment(null);
        $this->controller->addAction();
    }

    public function testEditActionGet(): void
    {
        $result = $this->controller->editAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testEditActionGetExceptionExpected(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current department can\'t be null');

        $this->controller->setCurrentDepartment(null);
        $this->controller->editAction();
    }

    public function testEditActionPost(): void
    {
        $this->controller->setPost(true);
        $shipment = new Shipment();
        $container = new Container();
        $container->setShipment($shipment);
        $containerItem = new ContainerItem();
        $containerItem->setContainer($container);
        $this->service
            ->expects($this->once())
            ->method('find')
            ->willReturn($containerItem);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $result = $this->controller->editAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testEditActionPostErrorExpected(): void
    {
        $this->controller->setPost(true);
        $shipment = new Shipment();
        $container = new Container();
        $container->setShipment($shipment);
        $containerItem = new ContainerItem();
        $containerItem->setContainer($container);
        $this->service
            ->expects($this->once())
            ->method('find')
            ->willReturn($containerItem);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $result = $this->controller->editAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(400, $result->getStatusCode());
    }

    public function testDeleteAction(): void
    {
        $result = $this->controller->deleteAction();
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(301, $result->getStatusCode());
    }
}
