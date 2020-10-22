<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Reference\Entity\Department;
use Storage\Entity\Purchase;
use Storage\Repository\PurchaseRepository;
use Zend\Validator\Date;

class PurchaseService extends AbstractService
{
    protected function getRepository(): PurchaseRepository
    {
        return parent::getRepository();
    }

    public function getTableListData(array $params, ?Department $currentDepartment, string $storageType): array
    {
        //В общем приходе не показываем данные скрытых подразделений
        $params['visibleOnly'] = $currentDepartment === null;
        $params['department'] = $currentDepartment === null ? null : $currentDepartment->getId();
        $dateValidator = new Date();
        if (! $dateValidator->isValid($params['startdate'])) {
            $params['startdate'] = date('Y-m-01');
        }
        if (! $dateValidator->isValid($params['enddate'])) {
            $params['enddate'] = date('Y-m-t');
        }
        $itemList = $this->getRepository()->getTableListData($params, $storageType);
        $result = [];
        $sum = 0;
        $sumFormal = 0;
        $weight = 0;
        $addedDate = null;
        /** @var Purchase $row */
        foreach ($itemList as $row) {
            $key = $row->getDate();
            if ($currentDepartment !== null && $row->getDeal() !== null && ! $currentDepartment->isBlack()) {
                $key .= $row->getDeal()->getId();
            }

            if (! array_key_exists($key, $result)) {
                $date = $row->getDate();
                if ($addedDate !== $date) {
                    $date = $row->getDate();
                    $addedDate = $date;
                } else {
                    $date = null;
                }
                $result[$key] = [
                    'deal' => $row->getDeal(),
                    'date' => $date,
                    'isPaid' => $row->getDeal() && $row->getDeal()->isPaid(),
                    'data' => [],
                    'weight' => 0,
                    'sum' => 0
                ];
            }

            $result[$key]['data'][] = $row;
            $result[$key]['weight'] += $row->getWeight();
            $result[$key]['sum'] += $row->getSum();

            $weight += $row->getWeight();
            $sum += $row->getSum();
            if ((int)$row->getSumFormal() === 0) {
                $sumFormal += $row->getSum();
            } else {
                $sumFormal += $row->getSumFormal();
            }
        }
        $total = $this->getRepository()->getTotal($params, $storageType);

        return [
            'result' => $result,
            'total' => $total,
            'weight' => $weight,
            'sum' => $sum,
            'sumFormal' => $sumFormal,
        ];
    }

    public function getByDeal(int $id): array
    {
        return $this->getRepository()->getByDeal($id);
    }

    public function getWeightByMetal(string $dateFrom, string $dateTo, ?int $departmentId): array
    {
        $weightList = $this->getRepository()->getWeightByMetal($dateFrom, $dateTo, $departmentId);
        $result = [];
        foreach ($weightList as $item) {
            $result[$item['id']] = $item['sum'];
        }
        return $result;
    }

    /**
     * Возвращает сумиррованный приход за указанный день сгруппированный по виду лома
     * @param $date
     * @param $departmentId
     * @return mixed
     */
    public function getGroupPurchaseByDay(string $date, int $departmentId)
    {
        $result = $this->getRepository()->getGroupPurchaseByDay($date, $departmentId);
        $arrayResult = [];
        foreach ($result as $item) {
            $arrayResult[$item['metal']] = $item['weight'];
        }
        return $arrayResult;
    }

    /**
     * Проверяет совпадает ли суммарный приход за день(уже забитый + забиваемый сейчас) с экспортом с подразделения
     * @param $purchaseList
     * @param $exportPurchaseList
     * @param $existsPurchaseList
     * @return bool
     */
    public function checkEqualDayAndExportPurchase($purchaseList, $exportPurchaseList, $existsPurchaseList): bool
    {
        $requestPurchaseList = [];
        foreach ($purchaseList as $purchase) {
            $metalName = $purchase['metal']['name'];

            if (! isset($requestPurchaseList[$metalName])) {
                $requestPurchaseList[$metalName] = $purchase['weight'];
            } else {
                $requestPurchaseList[$metalName] += $purchase['weight'];
            }
        }

        $aggregatePurchaseList = array_merge_recursive($existsPurchaseList, $requestPurchaseList);

        $aggregatePurchaseList = array_map(function ($item) {
            if (is_array($item)) {
                return array_sum($item);
            }
            return (float)$item;
        }, $aggregatePurchaseList);

        return empty(array_diff_assoc($exportPurchaseList, $aggregatePurchaseList));
    }

    public function getTotalByGroup(string $dateFrom, string $dateTo)
    {
        return $this->getRepository()->getTotalByGroup($dateFrom, $dateTo);
    }

    public function findByDateAndDepartment(string $date, int $departmentId)
    {
        return $this->getRepository()->findByDateAndDepartment($date, $departmentId);
    }

    /**
     * @param $purchaseList
     * @param string $purchaseDate
     * @param $currentDepartment
     * @return array
     */
    public function makeEntityList($purchaseList, string $purchaseDate, Department $currentDepartment): array
    {
        $list = [];
        foreach ($purchaseList as $purchase) {
            $list[] = $this->makeEntity($purchase, $purchaseDate, $currentDepartment);
        }
        return $list;
    }

    /**
     * @param array $purchaseData
     * @param $purchaseDate
     * @param Department $currentDepartment
     * @return Purchase
     */
    private function makeEntity(array $purchaseData, $purchaseDate, Department $currentDepartment): Purchase
    {
        $purchase = new Purchase();
        $purchase->setDate($purchaseDate);
        $purchase->setDepartment($currentDepartment);

        $customerRef = $this->getRepository()->getCustomerReference($purchaseData['customer']['id']);
        $purchase->setCustomer($customerRef);

        $metalRef = $this->getRepository()->getMetalReference($purchaseData['metal']['id']);
        $purchase->setMetal($metalRef);

        $purchase->setCost($purchaseData['cost']);
        $purchase->setFormal($purchaseData['formal']);
        $purchase->setWeight($purchaseData['weight']);
        return $purchase;
    }
}
