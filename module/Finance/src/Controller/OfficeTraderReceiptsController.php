<?php

namespace Finance\Controller;

use Zend\View\Model\ViewModel;

class OfficeTraderReceiptsController extends TraderReceiptsController
{
    protected string $indexRoute = 'office_trader_receipts';

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('finance/trader-receipts/index');

        return $viewModel;
    }
}
