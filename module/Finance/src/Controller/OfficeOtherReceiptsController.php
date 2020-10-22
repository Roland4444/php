<?php

namespace Finance\Controller;

use Zend\View\Model\ViewModel;

class OfficeOtherReceiptsController extends OtherReceiptsController
{
    protected string $indexRoute = 'office_other_receipts';

    public function indexAction(): ViewModel
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('finance/other-receipts/index');

        return $viewModel;
    }
}
