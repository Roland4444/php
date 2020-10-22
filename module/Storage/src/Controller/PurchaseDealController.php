<?php

namespace Storage\Controller;

use Core\Traits\RouteParams;
use Storage\Entity\Check;
use Storage\Form\PurchaseDealForm;
use Storage\Service\PurchaseDealService;
use Storage\Service\PurchaseService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PurchaseDealController extends AbstractActionController
{
    use RouteParams;

    private string $indexRoute = 'purchase_deal';

    private PurchaseDealService $dealService;
    private PurchaseService $purchaseService;
    private PurchaseDealForm $form;

    public function __construct($dealService, $purchaseService, $form)
    {
        $this->dealService = $dealService;
        $this->purchaseService = $purchaseService;
        $this->form = $form;
    }

    /**
     * Чек с QR кодом
     *
     * @return ViewModel
     */
    public function checkAction(): ViewModel
    {
        $id = $this->getRouteId();

        $deal = $this->dealService->find($id);
        $purchases = $this->purchaseService->getByDeal($id);
        $check = new Check($purchases);

        $view = new ViewModel([
            'deal' => $deal,
            'date' => date('d.m.Y H:i', strtotime($purchases[0]->getCreatedAt())),
            'checkData' => $check
        ]);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Редактирование комментария сделки.
     */
    public function editAction()
    {
        $id = $this->getRouteId();
        $deal = $this->dealService->find($id);
        $this->form->bind($deal);

        $departmentId = $this->currentDepartment()->getId();

        if ($this->getRequest()->isPost()) {
            $this->form->setInputFilter($deal->getInputFilter());
            $this->form->setData($this->getRequest()->getPost());

            if ($this->form->isValid()) {
                $this->dealService->save($deal);
                return $this->redirect()->toRoute('purchase', ['department' => $departmentId]);
            }
        }

        return new ViewModel([
            'title' => 'Редактирование сделки',
            'form' => $this->form,
            'id' => $id,
            'departmentId' => $departmentId,
            'indexRoute' => $this->indexRoute,
        ]);
    }
}
