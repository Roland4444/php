<?php

namespace Storage\Controller;

use Reference\Entity\Department;
use Reference\Service\DepartmentService;

class OfficeCashInController extends CashInController
{
    protected string $indexRoute = 'office_cash_in';

    /**
     * {@inheritdoc}
     */
    protected function getCurrentDepartmentId(): int
    {
        return $this->getCurrentDepartment()->getId();
    }

    protected function getCurrentDepartment(): Department
    {
        return $this->services[DepartmentService::class]->findByAlias('officecash');
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('storage/cash-in/index');
        return $viewModel;
    }

    /**
     * {@inheritdoc}
     */
    public function addAction()
    {
        $viewModel = parent::addAction();
        $viewModel->setTemplate('storage/cash-in/add');
        return $viewModel;
    }
}
