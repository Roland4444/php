<?php

namespace Factoring\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Factoring\Service\PaymentService;
use Factoring\Service\SalesService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TotalController extends AbstractActionController
{
    use FilterableController;

    private $salesService;
    private $paymentService;
    private $providerService;
    private $assignmentDebtService;

    public function __construct(SalesService $salesService, PaymentService $paymentService, $providerService, $assignmentDebtService)
    {
        $this->salesService = $salesService;
        $this->paymentService = $paymentService;
        $this->providerService = $providerService;
        $this->assignmentDebtService = $assignmentDebtService;
    }

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'factoring_total');
        $params = $filterForm->getFilterParams('factoring_total');

        $data = $this->getTableListData($params);

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'data' => $data,
        ]);
    }

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        $provider = $this->providerService->find(1);
        $salesSum = $this->salesService->getSumFactoring($params['enddate']);
        $assignmentDebtSum = $this->assignmentDebtService->getSumFactoring($params['enddate']);
        $paymentSum = $this->paymentService->getSumFactoring($params['enddate']);
        $sum = $salesSum + $assignmentDebtSum - $paymentSum;

        return [
            'provider' => $provider,
            'sales' => $salesSum,
            'assignmentDebt' => $assignmentDebtSum,
            'payment' => $paymentSum,
            'sum' => $sum
        ];
    }

    /**
     * Filter form
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement());
    }
}
