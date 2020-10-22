<?php

namespace Modules\Service;

use Finance\Service\OtherExpenseService;
use Modules\Repository\ContainerExtraRepository;
use Reference\Entity\ContainerOwner;

class ContainerRentalService
{
    /** @var OtherExpenseService*/
    private $mainOtherExpenseService;

    /** @var ContainerExtraRepository  */
    private $repository;

    public function __construct($mainOtherExpenseService, $repository)
    {
        $this->mainOtherExpenseService = $mainOtherExpenseService;
        $this->repository = $repository;
    }

    public function getTotalByCompanies($companies, $dateFrom, $dateTo)
    {
        $total = [];
        $rentalData = $this->getRentalGroupByCompany($dateFrom, $dateTo);
        $totalRentalData = $this->getRentalGroupByCompany('2000-01-01', $dateTo);
        /** @var ContainerOwner $company */
        foreach ($companies as $company) {
            $inns = explode(",", $company->getInn());
            $debit = 0;
            $totalDebit = 0;
            foreach ($inns as $inn) {
                $debit += $this->mainOtherExpenseService->getSumByInn($inn, $dateFrom, $dateTo);
                $totalDebit += $this->mainOtherExpenseService->getSumByInn($inn, '2000-01-01', $dateTo);
            }
            $credit = $rentalData[$company->getId()]['sum'] ?? 0;
            $totalCredit = $totalRentalData[$company->getId()]['sum'] ?? 0;
            $countOfContainers = $rentalData[$company->getId()]['count'] ?? 0;

            $arr = [
                'name' => $company->getName(),
                'debit' => $debit,
                'credit' => $credit,
                'count' => $countOfContainers,
                'balance' => $totalDebit - $totalCredit,
            ];

            if ($arr['balance'] != 0) {
                $total[] = $arr;
            }
        }
        return $total;
    }

    private function getRentalGroupByCompany($dateFrom, $dateTo): array
    {
        $data = $this->repository->getRentalGroupByCompany($dateFrom, $dateTo);
        $result = [];
        foreach ($data as $item) {
            $result[$item['id']] = $item;
        }
        return $result;
    }
}
