<?php

namespace Modules\Controller;

use Core\Traits\RestMethods;
use Modules\Service\CompletedVehicleTripsService;
use Zend\I18n\View\Helper\NumberFormat;
use Zend\Mvc\Controller\AbstractActionController;

class IncomeController extends AbstractActionController
{
    use RestMethods;

    private CompletedVehicleTripsService $service;

    public function __construct(CompletedVehicleTripsService $service)
    {
        $this->service = $service;
    }

    public function listAction()
    {
        $dateFrom = $this->getRequest()->getPost('startdate');
        $dateTo = $this->getRequest()->getPost('enddate');
        if (! is_numeric(strtotime($dateFrom)) || ! is_numeric(strtotime($dateTo))) {
            return $this->responseError('Не корректно указаны даты');
        }
        $incasVehicleInitDate = '2020-04-01';
        if ($dateFrom < $incasVehicleInitDate) {
            $dateFrom = $incasVehicleInitDate;
        }
        $data = $this->service->getByPeriod($dateFrom, $dateTo);
        $moneySum = 0;
        foreach ($data as $row) {
            $moneySum += $row->getPayment();
        }

        $total = $this->service->getMoneyBalance($incasVehicleInitDate, $dateTo);

        $numberFormat = new NumberFormat();
        return $this->responseSuccess([
            'rows' => $data,
            'moneySum' => $numberFormat($moneySum, \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, 'ru_RU'),
            'total' => $numberFormat($total, \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, 'ru_RU'),
        ]);
    }
}
