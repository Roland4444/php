<?php

namespace Reports\Controller;

use Application\Form\Filter\FilterableController;
use Finance\Service\OtherExpenseService;
use Finance\Service\OtherReceiptService;
use Modules\Service\CompletedVehicleTripsService;
use Reports\Facade\Reports;
use Storage\Facade\Storage;
use Storage\Service\PurchaseService;
use Storage\Service\ShipmentService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;

class ProfitController extends AbstractActionController
{
    use FilterableController;

    private OtherReceiptService $otherReceiptsService;
    private Storage $storage;
    private Reports $reports;
    private OtherExpenseService $financeOtherExpenseService;
    private ShipmentService $shipmentService;
    private PurchaseService $purchaseService;
    private CompletedVehicleTripsService $vehicleService;

    public function __construct($container)
    {
        $this->otherReceiptsService = $container->get(OtherReceiptService::class);
        $this->storage = $container->get('storage');
        $this->reports = $container->get('reports');
        $this->financeOtherExpenseService = $container->get('financeOtherExpenseService');
        $this->shipmentService = $container->get(ShipmentService::class);
        $this->purchaseService = $container->get(PurchaseService::class);
        $this->vehicleService = $container->get(CompletedVehicleTripsService::class);
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

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'reportProfit');
        $params = $filterForm->getFilterParams('reportProfit');
        $mainOthersExpense = $this->financeOtherExpenseService->getSumByCategory($params['startdate'], $params['enddate']);

        $groups = [];
        $mainOthersExpenseRes = [];
        $mainOthersExpenseSum = 0;
        foreach ($mainOthersExpense as $expense) {
            if (! in_array($expense['gr'], $groups)) {
                $groups[] = $expense['gr'];
                $group = [];
                $group['group'] = $expense['gr'];
                $group['gid'] = $expense['gid'];
                $group['money'] = $expense['money'];
                $group['data'][] = [
                    'category' => $expense['category'],
                    'money' => $expense['money'],
                ];
                $mainOthersExpenseRes[$expense['gr']] = $group;
            } else {
                $mainOthersExpenseRes[$expense['gr']]['data'][] = [
                    'category' => $expense['category'],
                    'money' => $expense['money'],
                ];
                $mainOthersExpenseRes[$expense['gr']]['money'] += $expense['money'];
            }
            $mainOthersExpenseSum += $expense['money'];
        }

        $othersExpenseArr = [];
        $full_other_expense_sum = 0;
        $othersExpense = $this->reports->getOfficeExpenseSumByCategory($params['startdate'], $params['enddate']);

        $groups = [];
        $othersExpenseRes = [];
        $othersExpenseSum = 0;
        foreach ($othersExpense as $expense) {
            if (! in_array($expense['gr'], $groups)) {
                $groups[] = $expense['gr'];
                $group = [];
                $group['group'] = $expense['gr'];
                $group['gid'] = $expense['gid'];
                $group['money'] = $expense['money'];
                $group['data'][] = [
                    'category' => $expense['category'],
                    'money' => $expense['money'],
                ];
                $othersExpenseRes[$expense['gr']] = $group;
            } else {
                $othersExpenseRes[$expense['gr']]['data'][] = [
                    'category' => $expense['category'],
                    'money' => $expense['money'],
                ];
                $othersExpenseRes[$expense['gr']]['money'] += $expense['money'];
            }
            $othersExpenseSum += $expense['money'];
        }
        $full_other_expense_sum += $othersExpenseSum;
        if ($othersExpenseSum > 0) {
            $othersExpenseArr[] = [
                'dep' => '123',
                'expense' => $othersExpenseRes,
                'sum' => $othersExpenseSum,
            ];
        }
        $rental = $this->storage->getOwnerCostSumByDate($params['startdate'], $params['enddate']);

        $add = [];
        $add['data'][] = [
            'name' => 'Пользование',
            'sum' => $rental,
        ];
        $add['sum'] = $rental;

        $shipmentTotal = $this->shipmentService->getTotalByGroup($params['startdate'], $params['enddate']);

        $purchasetotal = $this->purchaseService->getTotalByGroup($params['startdate'], $params['enddate']);
        $total = [];
        $profit = 0;
        foreach ($purchasetotal as $purchase) {
            foreach ($shipmentTotal as $shipment) {
                if ($purchase['metal'] == $shipment['group']) {
                    $total[] = [
                        'group' => $purchase['metal'],
                        'pur_price' => $purchase['price'],
                        'ship_price' => $shipment['price'],
                        'weight' => $shipment['weight'],
                        'dif' => $shipment['price'] - $purchase['price'],
                        'profit' => ($shipment['price'] - $purchase['price']) * $shipment['weight'],
                    ];
                    $profit += ($shipment['price'] - $purchase['price']) * $shipment['weight'];
                }
            }
        }

        $otherReceipts = $this->otherReceiptsService->getSumByPeriodAndBank($params['startdate'], $params['enddate']);

        $moves = $this->vehicleService->getTotalSumByDepartment($params['startdate'], $params['enddate']);

        $expenses = $mainOthersExpenseSum + $full_other_expense_sum + $rental;
        $net_profit = $profit - $expenses + $moves;

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'main_expense' => $mainOthersExpenseRes,
            'main_sum' => $mainOthersExpenseSum,
            'storage_expense' => $othersExpenseArr,
            'add' => $add,
            'total' => $total,
            'profit' => $profit,
            'net_profit' => $net_profit,
            'receipts' => $otherReceipts,
            'moves' => $moves,
        ]);
    }
}
