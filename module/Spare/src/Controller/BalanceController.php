<?php

namespace Spare\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BalanceController extends AbstractActionController
{
    use FilterableController;

    private $balanceService;

    public function __construct($balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function indexAction()
    {
        $form = $this->filterForm($this->getRequest(), 'spareBalance');
        $params = $form->getFilterParams('spareBalance');

        $balanceData = $this->balanceService->getTotal($params['enddate']);

        return new ViewModel([
            'balanceData' => $balanceData,
            'form' => $form->getForm($params)
        ]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement((new DateElement()));
    }
}
