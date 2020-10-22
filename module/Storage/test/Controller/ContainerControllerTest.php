<?php

namespace StorageTest\Controller;

use Core\Service\AccessValidateService;
use PHPUnit\Framework\TestCase;
use Reference\Service\ContainerOwnerService;
use Storage\Controller\ContainerController;
use Storage\Entity\Container;
use Storage\Entity\ContainerExtraOwner;
use Storage\Entity\Shipment;
use Storage\Form\ContainerForm;
use Storage\Service\ContainerService;
use StorageTest\Controller\Mock\MockContainerController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ContainerControllerTest extends TestCase
{
    private ContainerController $controller;
    private ContainerService $containerService;
    private ContainerOwnerService $containerOwnerService;
    private AccessValidateService $accessValidateService;
    private ContainerForm $form;

    protected function setUp(): void
    {
        $this->containerService = $this->createMock(ContainerService::class);
        $this->containerOwnerService = $this->createMock(ContainerOwnerService::class);
        $this->accessValidateService = $this->createMock(AccessValidateService::class);
        $this->form = $this->createMock(ContainerForm::class);
        $this->controller = new MockContainerController($this->containerService, $this->containerOwnerService, $this->accessValidateService, $this->form);
    }

    public function testAddAction(): void
    {
        $this->controller->setRouteParam('container', 0);
        $result = $this->controller->addAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testAddActionWithContainerId(): void
    {
        $this->controller->setRouteParam('container', 123);
        $this->containerService
            ->expects($this->once())
            ->method('find')
            ->willReturn(new Container());

        $result = $this->controller->addAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testEditActionGet(): void
    {
        $fakeContainer = new Container();
        $fakeContainer->setShipment(new Shipment());
        $this->containerService
            ->expects($this->once())
            ->method('find')
            ->willReturn($fakeContainer);
        $result = $this->controller->editAction();
        $this->assertInstanceOf(ViewModel::class, $result);
    }

    public function testEditActionPost(): void
    {
        $container = new Container();
        $container->setShipment(new Shipment());
        $container->setExtraOwner(new ContainerExtraOwner());
        $this->containerService
            ->expects($this->once())
            ->method('find')
            ->willReturn($container);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->controller->setPost(true);

        $result = $this->controller->editAction();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testDeleteAction(): void
    {
        $result = $this->controller->deleteAction();
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testGetColorDepartmentsContainersByDateAction(): void
    {
        $this->controller->setRouteParam('container', 123);

        $this->containerService
            ->expects($this->once())
            ->method('getColorDepartmentsContainersByDate');

        $result = $this->controller->getColorDepartmentsContainersByDateAction();

        $this->assertInstanceOf(Response::class, $result);
    }
}
