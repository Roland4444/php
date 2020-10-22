<?php


namespace Storage\Repository\Interfaces;

interface MetalExpenseRepositoryInterface
{
    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId, $customerId = null);

    public function getSum($params);

    public function findAll();

    public function getSumByDeal(int $dealId): ?float;
}
