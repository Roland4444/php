<?php

namespace Finance\Controller;

use Zend\View\Model\ViewModel;

class OfficeOtherExpenseController extends OtherExpenseController
{
    protected bool $withoutCash = true;

    protected string $indexRoute = 'office_bank_expense';

    /**
     * {@inheritdoc}
     */
    public function indexAction(): ViewModel
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('finance/other-expense/index');
        return $viewModel;
    }
}
