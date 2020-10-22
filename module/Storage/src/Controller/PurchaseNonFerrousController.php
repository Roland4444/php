<?php

namespace Storage\Controller;

class PurchaseNonFerrousController extends PurchaseController
{
    protected string $indexRoute = 'purchaseNonFerrous';
    protected string $storageType = 'color';

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
        $viewModel = parent::indexAction();
        $viewModel->setTemplate('storage/purchase/index');
        return $viewModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissions(): array
    {
        return [
            'add' => false,
            'edit' => false,
            'delete' => false,
            'deal' => false
        ];
    }
}
