<?php
namespace Storage\Service;

use Exception;
use Finance\Service\MoneyToDepartmentService;
use Storage\Dao\CashTotalDao;
use Storage\Entity\CustomerTotal;
use Storage\Entity\CustomerTotalList;

class CashService
{
    protected MoneyToDepartmentService $moneyToDepartmentService;
    protected MetalExpenseService $storageMetalExpenseService;
    private CashTransferService $cashTransferService;
    protected CashTotalDao $dao;

    private const ZERO_DATE = '2000-01-01';

    /**
     * CashService constructor.
     * @param MoneyToDepartmentService $moneyToDepartmentService
     * @param MetalExpenseService $storageMetalExpenseService
     * @param CashTransferService $cashTransferService
     * @param $dao
     */
    public function __construct($moneyToDepartmentService, $storageMetalExpenseService, $cashTransferService, $dao)
    {
        $this->moneyToDepartmentService = $moneyToDepartmentService;
        $this->storageMetalExpenseService = $storageMetalExpenseService;
        $this->cashTransferService = $cashTransferService;
        $this->dao = $dao;
    }

    public function getSummary($dateFrom, $dateTo, $departmentId): array
    {
        return [
            'cash-in' => $this->moneyToDepartmentService->getTotalSumByDepartment($dateFrom, $dateTo, $departmentId),
            'metalExpense' => $this->storageMetalExpenseService->getTotalSumByDepartment($dateFrom, $dateTo, $departmentId),
            'total' => $this->getTotalSumByDepartment($dateTo, $departmentId)
        ];
    }

    public function getTotalByDepartment(int $departmentId, $dateEnd)
    {
        $total = $this->dao->getTotalByDepartment($departmentId, $dateEnd);
        $customerTotalList = new CustomerTotalList();
        foreach ($total as $customer) {
            $customer['main_payment'] = 0;
            $customerTotal = new CustomerTotal($customer);
            if ($customerTotal->isLegal()) {
                $customerTotal->setPurchaseFactForAmountSum($customer['purchase'] - $customer['formal_sum']);
            }
            $customerTotalList->addItem($customerTotal);
        }
        return $customerTotalList;
    }

    public function getTotal($dateEnd)
    {
        $total = $this->dao->getTotal($dateEnd);
        $customerTotalList = new CustomerTotalList();
        foreach ($total as $customer) {
            $customerTotal = new CustomerTotal($customer);
            $customerTotal->setRowClass($this->getRowStyle($customerTotal->getInspectionDate()));
            $customerTotalList->addItem($customerTotal);
        }
        return $customerTotalList;
    }

    /**
     * Return css class for row
     *
     * @param string Дата
     * @return int
     * @throws Exception
     */
    private function getRowStyle($date)
    {
        $className = '';
        if (empty($date)) {
            $className = 'danger';
        }
        $dateStamp = strtotime($date);
        $dateMonthAgo = (new \DateTime())->modify('-1 month')->getTimestamp();

        if ($dateMonthAgo > $dateStamp) {
            $className = 'danger';
        }
        $dateWeekAgo = (new \DateTime())->modify('-7 day')->getTimestamp();
        if ($dateWeekAgo > $dateStamp) {
            $className = 'warning';
        }
        return $className;
    }

    private function getTotalSumByDepartment($dateTo, $departmentId)
    {
        $cashIn = $this->moneyToDepartmentService->getTotalSumByDepartment(self::ZERO_DATE, $dateTo, $departmentId);
        $metalExpense = $this->storageMetalExpenseService->getTotalSumByDepartment(self::ZERO_DATE, $dateTo, $departmentId);
        $transfer = $this->cashTransferService->getBalance($dateTo, $departmentId);

        return $cashIn - $metalExpense + $transfer;
    }
}
