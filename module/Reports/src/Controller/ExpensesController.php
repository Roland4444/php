<?php

namespace Reports\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Core\Traits\RestMethods;
use Reports\Service\ExpensesReportService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ExpensesController extends AbstractActionController
{
    use FilterableController;
    use RestMethods;

    private $service;

    public function __construct(ExpensesReportService $service)
    {
        $this->service = $service;
    }

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'reportExpenses');
        $params = $filterForm->getFilterParams('reportExpenses');

        $groupedExpenses = $this->service->getExpenses($params['startdate'], $params['enddate']);

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'groupedExpenses' => $groupedExpenses
        ]);
    }

    protected function getFilterForm()
    {
        return new SubmitElement((new DateElement()));
    }

    public function limitsAction()
    {
        $data = $this->service->getLimits();

        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getContent();
                $this->service->saveLimits($data);
                return $this->responseSuccess();
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }
        }
        return new ViewModel([
            'data' => json_encode($data)
        ]);
    }
}
