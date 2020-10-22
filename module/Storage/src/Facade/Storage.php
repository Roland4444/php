<?php

namespace Storage\Facade;

use OfficeCash\Facade\OfficeCash;
use Storage\Entity\Container;
use Storage\Service\ContainerService;
use Storage\Service\ShipmentService;

class Storage
{
    private ShipmentService $shipmentService;
    private ContainerService $containerService;
    private OfficeCash $officeCash;

    public function __construct($shipmentService, $containerService, $officeCash)
    {
        $this->shipmentService = $shipmentService;
        $this->containerService = $containerService;
        $this->officeCash = $officeCash;
    }

    public function findShipmentsWithFactoring($dateStart, $dateEnd): array
    {
        return $this->toArray($this->shipmentService->findWithFactoring($dateStart, $dateEnd));
    }

    public function getSumShipmentWithFactoring($dateEnd): ?float
    {
        $sumRub = $this->shipmentService->getSumFactoringRub($dateEnd);
        $sumDol = $this->shipmentService->getSumFactoringDol($dateEnd);
        return $sumDol + $sumRub;
    }

    public function findContainersByOwnerAndDates(?int $ownerId, ?string $dateFrom, ?string $dateTo): array
    {
        return $this->toArray($this->containerService->findByOwnerAndDates($ownerId, $dateFrom, $dateTo));
    }

    public function getContainersSumByOwnerFormalOrdered($ownerId = null, $dateFrom = null, $dateTo = null): ?float
    {
        return $this->containerService->getSumByOwnerFormalOrdered($ownerId, $dateFrom, $dateTo);
    }

    public function getExtraOwnerDataByContainerId(int $id): array
    {
        /** @var Container $container */
        $container = $this->containerService->find($id);
        return $this->toArray($container->getExtraOwner());
    }

    public function saveContainerExtraOwner(int $containerId, array $postData): void
    {
        /** @var Container $container */
        $container = $this->containerService->find($containerId);
        $extraOwner = $container->getExtraOwner();
        $extraOwner->setDateFormal($postData['date_formal']);
        $extraOwner->setOwnerCost($postData['money']);
        $extraOwner->setIsPaid($postData['is_paid']);
        $this->containerService->save($container);
    }

    public function getOwnerCostSumByDate($dateFrom, $dateTo)
    {
        return $this->containerService->getOwnerCostSumByDate($dateFrom, $dateTo);
    }

    private function toArray($arrayOfObjects): array
    {
        return json_decode(json_encode($arrayOfObjects), true);
    }

    public function getExpenseTotalSumByDepartment(string $dateFrom, $dateTo, ?int $departmentId)
    {
        return $this->officeCash->getExpenseTotalSumByDepartment($dateFrom, $dateTo, $departmentId);
    }
}
