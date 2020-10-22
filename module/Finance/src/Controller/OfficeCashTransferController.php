<?php

namespace Finance\Controller;

use Zend\View\Model\ViewModel;

class OfficeCashTransferController extends CashTransferController
{
    protected string $indexRoute = 'office_cash_transfer';

    public function indexAction(): ViewModel
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('finance/cash-transfer/index');

        return $viewModel;
    }

    public function addAction()
    {
        $viewModel = parent::addAction();
        if ($viewModel instanceof \Zend\View\Model\ViewModel) {
            $viewModel->setTemplate('finance/cash-transfer/add');
        }

        return $viewModel;
    }

    public function editAction()
    {
        $viewModel = parent::editAction();
        if ($viewModel instanceof \Zend\View\Model\ViewModel) {
            $viewModel->setTemplate('finance/cash-transfer/edit');
        }
        return $viewModel;
    }
}
