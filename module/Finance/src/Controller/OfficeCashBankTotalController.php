<?php

namespace Finance\Controller;

use Zend\View\Model\ViewModel;

class OfficeCashBankTotalController extends TotalController
{
    protected bool $withoutCash = true;
    protected string $indexRoute = 'office_bank_total';

    public function indexAction(): ViewModel
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('finance/total/index');
        return $viewModel;
    }
}
