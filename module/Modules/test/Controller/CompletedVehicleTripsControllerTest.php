<?php

namespace ModuleTest\Controller;

use Doctrine\ORM\EntityManager;
use Modules\Controller\CompletedVehicleTripsController;
use Modules\Form\MoveVehiclesEditForm;
use Zend\Log\Logger;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ProjectTest\Bootstrap;

class CompletedVehicleTripsControllerTest extends AbstractHttpControllerTestCase
{
    private EntityManager $entityManager;
    private Logger $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(Logger::class);
    }

    /**
     * Проверка получения формы для редактирования заверщенного выезда
     *
     * @throws \ReflectionException
     */
    public function testGetEditForm()
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $instance = new CompletedVehicleTripsController($this->entityManager, $this->logger, null, []);

        $method = new \ReflectionMethod($instance, 'getEditForm');
        $method->setAccessible(true);
        $this->assertInstanceOf(MoveVehiclesEditForm::class, $method->invoke($instance));
    }
}
