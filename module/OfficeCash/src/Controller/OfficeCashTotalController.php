<?php

namespace OfficeCash\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Finance\Service\MoneyToDepartmentService;
use OfficeCash\Service\TransportIncomeService;
use Reference\Service\DepartmentService;
use OfficeCash\Service\OtherExpenseService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OfficeCashTotalController extends AbstractActionController
{
    use FilterableController;

    private DepartmentService $departmentService;
    private TransportIncomeService $transportIncomeService;
    private MoneyToDepartmentService $moneyToDepartmentService;
    private OtherExpenseService $storageOtherExpenseService;

    public function __construct($departmentService, $transportIncomeService, $moneyToDepartmentService, $storageOtherExpenseService)
    {
        $this->departmentService = $departmentService;
        $this->moneyToDepartmentService = $moneyToDepartmentService;
        $this->transportIncomeService = $transportIncomeService;
        $this->storageOtherExpenseService = $storageOtherExpenseService;
    }

    /**
     * {@inheritdoc}
     */
    private function getCurrentDepartmentId(): int
    {
        $department = $this->departmentService->findByAlias('officecash');
        return $department->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'office_cash_total');
        $params = $filterForm->getFilterParams('office_cash_total');

        $departmentId = $this->getCurrentDepartmentId();

        $cashIn = $this->moneyToDepartmentService->getTotalSumByDepartment('2000-01-01', $params['enddate'], $departmentId);
        $expense = $this->storageOtherExpenseService->getTotalSumByDepartment('2000-01-01', $params['enddate'], $departmentId);
        $transportIncome = $this->transportIncomeService->getMoneySum('2000-01-01', $params['enddate']);
        $totalSum = $cashIn - $expense + $transportIncome;

        $cashInForMonth = $this->moneyToDepartmentService->getTotalSumByDepartment($params['startdate'], $params['enddate'], $departmentId);
        $expenseForMonth = $this->storageOtherExpenseService->getTotalSumByDepartment($params['startdate'], $params['enddate'], $departmentId);
        $transportIncomeForMonth = $this->transportIncomeService->getMoneySum($params['startdate'], $params['enddate']);
        return new ViewModel([
            'route' => 'office_cash_total',
            'form' => $filterForm->getForm($params),
            'cashIn' => $cashInForMonth,
            'expense' => $expenseForMonth,
            'transport' => $transportIncomeForMonth,
            'total' => $totalSum,
        ]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement());
    }
}
