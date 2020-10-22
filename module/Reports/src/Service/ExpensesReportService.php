<?php

namespace Reports\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Finance\Service\OtherExpenseService;
use Reports\Facade\Reports;

class ExpensesReportService
{
    private OtherExpenseService $financeExpenseService;
    private EntityManager $entityManager;
    private Reports $reports;

    public function __construct($financeExpenseService, $entityManager, $reports)
    {
        $this->financeExpenseService = $financeExpenseService;
        $this->entityManager = $entityManager;
        $this->reports = $reports;
    }

    /**
     * Возвращает json с лимитами расходов
     * @return mixed
     * @throws DBALException
     */
    public function getLimits()
    {
        $conn = $this->entityManager->getConnection();
        $sql = 'SELECT `data` FROM report_expense_limits WHERE id = 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return json_decode($result['data']);
    }

    public function saveLimits(string $data)
    {
        $conn = $this->entityManager->getConnection();
        $sql = 'UPDATE report_expense_limits SET `data` = :data WHERE id = 1';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('data', $data);
        $stmt->execute();
    }

    public function getExpenses(string $dateFrom, string $dateTo)
    {
        $mainExpenses = $this->financeExpenseService->getSumByCategory($dateFrom, $dateTo);
        $storageExpenses = $this->reports->getOfficeExpenseSumByCategory($dateFrom, $dateTo);

        $expenses = $this->mergeExpenses($mainExpenses, $storageExpenses);

        return $this->splitIntoGroups($expenses);
    }

    private function mergeExpenses(array $mainExpenses, array $storageExpenses): array
    {
        $expenses = [];
        foreach ($mainExpenses as $mainExpense) {
            $expenses[$mainExpense['category']] = $mainExpense;
        }
        foreach ($storageExpenses as $storageExpense) {
            $category = $storageExpense['category'];
            if (isset($expenses[$category])) {
                $expenses[$category]['money'] = (float)$expenses[$category]['money'] + (float)$storageExpense['money'];
            } else {
                $expenses[$category] = $storageExpense;
            }
        }
        return $expenses;
    }

    private function splitIntoGroups(array $expenses): array
    {
        $grouped = [];
        foreach ($expenses as $expense) {
            $grouped[$expense['gr']]['expenses'][] = $expense;
            $grouped[$expense['gr']]['sort'] = $expense['sort'];
        }
        foreach ($grouped as $key => $group) {
            $sum = 0;
            foreach ($group['expenses'] as $expense) {
                $sum += $expense['money'];
            }
            $grouped[$key]['title'] = $key;
            $grouped[$key]['sum'] = $sum;
        }
        $result = [];
        foreach ($grouped as $group) {
            $result[$group['sort']][] = $group;
        }
        ksort($result);
        return $result;
    }
}
