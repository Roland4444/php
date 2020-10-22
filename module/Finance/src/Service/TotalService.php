<?php

namespace Finance\Service;

use Application\Service\BaseService;
use Factoring\Facade\Factoring;
use Finance\Dao\TotalServiceDao;
use Finance\Entity\BankAccount;
use Interop\Container\ContainerInterface;
use Zend\I18n\View\Helper\CurrencyFormat;

class TotalService extends BaseService
{
    private const ZERO_DATE = '2000-01-01';

    private TraderReceiptsService $traderReceiptsService;
    private OtherReceiptService $otherReceiptsService;
    private OtherExpenseService $otherExpenseService;
    private MoneyToDepartmentService $moneyDepartmentService;
    private CashTransferService $cashTransferService;
    private Factoring $factoring;
    private MetalExpenseService $metalExpenseService;
    private BankService $bankService;

    /** @var TotalServiceDao */
    protected $dao;

    public function __construct(TotalServiceDao $dao, ContainerInterface $container)
    {
        $this->em = $container->get('entityManager');
        $this->dao = $dao;

        $this->metalExpenseService = $container->get('financeMetalExpense');
        $this->traderReceiptsService = $container->get('traderReceiptsService');
        $this->otherReceiptsService = $container->get(OtherReceiptService::class);
        $this->otherExpenseService = $container->get('financeOtherExpenseService');
        $this->moneyDepartmentService = $container->get(MoneyToDepartmentService::class);
        $this->cashTransferService = $container->get('cashTransferService');
        $this->factoring = $container->get('factoring');
        $this->bankService = $container->get(BankService::class);
    }

    public function getBalance(string $dateTo, bool $withoutCash = false)
    {
        $accounts = [
            'items' => [],
            'sum' => 0
        ];

        $overdrafts = $this->getOverdraft($dateTo);

        $bankAccounts = $withoutCash ? $this->bankService->findWithoutCash() : $this->bankService->findAll();

        $cashTransfer = $this->cashTransferService->getSumByPeriodGroupByBank(self::ZERO_DATE, $dateTo);
        $traderReceipt = $this->traderReceiptsService->getSumByPeriodGroupByBank(self::ZERO_DATE, $dateTo);
        $otherReceipt = $this->otherReceiptsService->getSumByPeriodGroupByBank(self::ZERO_DATE, $dateTo);
        $otherExpense = $this->otherExpenseService->getSumByPeriodGroupByBank(self::ZERO_DATE, $dateTo);
        $moneyDepartment = $this->moneyDepartmentService->getSumByPeriodGroupByBank(self::ZERO_DATE, $dateTo);
        $factoringSum = $this->factoring->getSumByPeriodGroupByBank($dateTo);
        $metalExpense = $this->metalExpenseService->getSumByDateGroupByBank($dateTo);
        $currencyFormatter = new CurrencyFormat();
        /** @var BankAccount $bank */
        foreach ($bankAccounts as $bank) {
            $bankId = $bank->getId();
            $income = $cashTransfer[$bankId] + $traderReceipt[$bankId] + $otherReceipt[$bankId] + $factoringSum[$bankId];
            $expense = $otherExpense[$bankId] + $moneyDepartment[$bankId] + $metalExpense[$bankId];
            $sum = $income - $expense;

            $accounts['items'][] = [
                'name' => $bank->getName(),
                'total' => $currencyFormatter($sum, 'RUR'),
                'overdraft' => ! empty($overdrafts[$bank->getId()])
                    ? $currencyFormatter($overdrafts[$bank->getId()], 'RUR')
                    : '',
                'bank' => $bank->getBank()
            ];
            $accounts['sum'] += $sum;
        }
        $accounts['sum'] = $currencyFormatter($sum, 'RUR');
        return $accounts;
    }

    public function getExpenses(string $dateFrom, string $dateTo): array
    {
        $traderReceiptSum = $this->traderReceiptsService->getSumByPeriodAndBank($dateFrom, $dateTo);
        $otherReceiptSum = $this->otherReceiptsService->getSumByPeriodAndBank($dateFrom, $dateTo);
        $otherExpenseSum = $this->otherExpenseService->getSumByPeriodAndBank($dateFrom, $dateTo);
        $moneyDepartmentSum = $this->moneyDepartmentService->getSumByPeriodAndBank($dateFrom, $dateTo);
        $currencyFormatter = new CurrencyFormat();
        return [
            'traderReceipts' => $currencyFormatter($traderReceiptSum, 'RUR'),
            'otherReceipts' => $currencyFormatter($otherReceiptSum, 'RUR'),
            'otherExpense' => $currencyFormatter($otherExpenseSum, 'RUR'),
            'moneyDepartment' => $currencyFormatter($moneyDepartmentSum, 'RUR'),
        ];
    }

    public function getTraderBalance(string $dateTo)
    {
        $totalData = $this->dao->getTotalByTraders($dateTo);
        $currencyFormatter = new CurrencyFormat();
        $traderParents = [
            'items' => [],
            'sum' => 0
        ];
        $factoringAssignmentDebtByTrader = $this->factoring->getAssignmentDebtGroupByTrader($dateTo);
        foreach ($totalData as $traderGroup) {
            $shipmentSum = $traderGroup['rub_sum'] + $traderGroup['dol_sum'];
            $traderTotal = $shipmentSum - $traderGroup['p_sum'];
            $traderId = $traderGroup['id'];
            if (! empty($factoringAssignmentDebtByTrader[$traderId])) {
                $traderTotal -= $factoringAssignmentDebtByTrader[$traderId];
            }

            $trader = [
                'id' => $traderId,
                'name' => $traderGroup['name'],
                'send' => $currencyFormatter($shipmentSum, 'RUR'),
                'payment' => $currencyFormatter($traderGroup['p_sum'], 'RUR'),
                'total' => $currencyFormatter($traderTotal, 'RUR')
            ];
            if (! isset($traderParents['items'][$traderGroup['ord']])) {
                $traderParents['items'][$traderGroup['ord']] = [
                    'id' => $traderGroup['parent_id'],
                    'name' => $traderGroup['parent_name'],
                    'traders' => [],
                    'sum' => $traderTotal,
                    'hide' => $traderGroup['hide'] == 1,
                ];
            } else {
                $traderParents['items'][$traderGroup['ord']]['sum'] += $traderTotal;
            }
            if (round($traderTotal) != 0) {
                $traderParents['items'][$traderGroup['ord']]['traders'][] = $trader;
                $traderParents['sum'] += $traderTotal;
            }
        }
        foreach ($traderParents['items'] as $key => $parent) {
            $traderParents['items'][$key]['sum'] = $currencyFormatter($parent['sum'], 'RUR');
        }
        $traderParents['sum'] = $currencyFormatter($traderParents['sum'], 'RUR');
        return $traderParents;
    }

    private function getOverdraft(string $dateTo): array
    {
        $results = $this->dao->getOverdraft($dateTo);

        $totals = [];
        if (empty($results)) {
            return $totals;
        }

        foreach ($results as $result) {
            $bankId = $result['id'];
            $receipts = (float) $result['receipts'];
            $expenses = (float) $result['expenses'];

            $totals[$bankId] = $receipts - $expenses;
        }
        return $totals;
    }
}
