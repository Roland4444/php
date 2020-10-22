<?php

namespace FactoringTest\Controller;

use Doctrine\ORM\EntityManager;
use Factoring\Entity\AssignmentDebt;
use Factoring\Form\AssignmentDebtForm;
use Factoring\Service\AssignmentDebtService;
use PHPUnit\Framework\TestCase;
use Storage\Plugin\CurrentDepartment;
use Zend\Log\Logger;

class AssignmentDebtControllerTest extends TestCase
{
    private EntityManager $entityManager;
    private Logger $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->logger = $this->createMock(Logger::class);
    }

    public function testIndexAction()
    {
        $service = $this->createMock(AssignmentDebtService::class);

        $item = new AssignmentDebt();
        $item->setMoney(100);
        $service->expects($this->once())
            ->method('findByPeriod')
            ->with(date('Y-m-01'), date('Y-m-t'))
            ->willReturn([$item]);

        $mockCurrentDepartment = $this->createMock(CurrentDepartment::class);
        $mockCurrentDepartment->expects($this->once())
            ->method('isHide')
            ->willReturn(false);

        $controller = new MockAssignmentDebtController($this->entityManager, $this->logger, $service, []);
        $controller->setMockCurrentDepartment($mockCurrentDepartment);
        $viewModel = $controller->indexAction();

        $this->assertIsArray($viewModel->getVariables());
    }

    public function testAddAction()
    {
        $service = $this->createMock(AssignmentDebtService::class);
        $form = $this->createMock(AssignmentDebtForm::class);
        $services = [
            'form' => $form
        ];

        $controller = new MockAssignmentDebtController($this->entityManager, $this->logger, $service, $services);
        $controller->initRequest('post');
        $viewModel = $controller->addAction();

        $this->assertIsArray($viewModel->getVariables());
    }

    public function testEditAction()
    {
        $form = $this->createMock(AssignmentDebtForm::class);
        $services = [
            'form' => $form
        ];

        $service = $this->createMock(AssignmentDebtService::class);

        $controller = new MockAssignmentDebtController($this->entityManager, $this->logger, $service, $services);
        $controller->initRequest('post');
        $viewModel = $controller->editAction();

        $this->assertIsArray($viewModel->getVariables());
    }
}
