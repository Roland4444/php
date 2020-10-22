<?php

namespace Spare\Service;

use Core\Service\AbstractService;
use Doctrine\ORM\ORMException;
use Spare\Entity\Planning;
use Spare\Entity\PlanningItems;
use Spare\Entity\PlanningStatus;
use Spare\Repositories\PlanningRepository;

/**
 * Class PlanningService
 * @package Spare\Service
 * @method  PlanningRepository getRepository() Метод класса AbstractService
 */
class PlanningService extends AbstractService
{
    private $planningStatusService;
    private $planningItemService;

    public function __construct(
        $repository,
        PlanningStatusService $planningStatusService,
        PlanningItemsService $planningItemService
    ) {
        $this->planningStatusService = $planningStatusService;
        $this->planningItemService = $planningItemService;
        parent::__construct($repository);
    }

    /**
     * Производит запрос на поиск всех заявок на запчасти с установленными фильтрами
     *
     * @param array $params
     * @return mixed
     */
    public function getTableListData($params)
    {
        return $this->getRepository()->getTableListData($params);
    }

    /**
     * Используется для передаче на фронт данных о заявках
     *
     * @param $param
     * @param bool $returnJson
     * @return array|false|string
     */
    public function getDataPlanning($param, $returnJson = true)
    {
        $planning = $this->getTableListData($param);
        if (empty($planning)) {
            return $returnJson ? '[]' : [];
        }
        $data = [];
        foreach ($planning as $planningItem) {
            $data[] = $this->getDataFromPlanning($planningItem, false);
        }
        if (! $returnJson) {
            return $data;
        }

        return json_encode($data);
    }

    /**
     * Формирует json для передачи данных в шаблон фронта
     *
     * @param Planning $planningEntity Заказ
     * @param boolean  $returnJson     Вернуть json
     *
     * @return false|string|array
     */
    public function getDataFromPlanning(Planning $planningEntity, $returnJson = true)
    {
        $planning = [
            'date' => $planningEntity->getDate(),
            'comment' => $planningEntity->getComment(),
            'status' => $planningEntity->getStatus()->getAlias(),
            'number' => $planningEntity->getId(),
            'employee' => empty($planningEntity->getEmployee()) ? '' : $planningEntity->getEmployee()->getId(),
            'vehicle' => empty($planningEntity->getVehicle()) ? '' : $planningEntity->getVehicle()->getId(),
        ];

        foreach ($planningEntity->getItems() as $planningItem) { /**@var  PlanningItems $planningItem*/
            $planning['data'][] = [
                'id' => $planningItem->getSpare()->getId(),
                'countInPlanning' => $planningItem->getQuantity(), //Количество в заявке
                'spareId' => $planningItem->getSpare()->getId(),
                'spareUnits' => $planningItem->getSpare()->getUnits(),
                'isComposite' => $planningItem->getSpare()->getIsComposite(),
                'nameSpare' => $planningItem->getSpare()->getName(),
                'countOrdered' => $planningItem->getOrdered(), //количество в заказах
                'planningItemId' => $planningItem->getId(),
            ];
        }

        if (! $returnJson) {
            return $planning;
        }

        return json_encode($planning);
    }

    /**
     * Меняет статус заявки по переданому массиву состоящих из id planningItems
     *
     * @param $itemIds
     * @throws
     */
    public function setStatusPlanningByItemIds($itemIds)
    {
        $statuses = $this->planningStatusService->getPlanningStatuses();
        $plannings = $this->getByItemIds($itemIds);
        if (empty($plannings)) {
            return;
        }
        $dateForUpdate = [];
        foreach ($plannings as $planning) {/**@var Planning $planning*/
            $countOrderedItem = 0;
            $countCloseItem = 0;
            foreach ($planning->getItems() as $planningItem) {/**@var PlanningItems $planningItem*/
                if ($planningItem->getOrdered() >= $planningItem->getQuantity()) {
                    $countOrderedItem++;
                }
                if ($planningItem->getReceipted() >= $planningItem->getQuantity()) {
                    $countCloseItem++;
                }
            }

            $status = $statuses[PlanningStatus::ALIAS_IN_WORK]->getId();

            if ($planning->getItems()->count() == $countOrderedItem) {
                $status = $statuses[PlanningStatus::ALIAS_ORDERED]->getId();
                if ($planning->getItems()->count() == $countCloseItem) {
                    $status = $statuses[PlanningStatus::ALIAS_CLOSED]->getId();
                }
            }

            $dateForUpdate[$status][] = $planning->getId();
        }

        foreach ($dateForUpdate as $status => $rows) {
            $ids = implode(',', $rows);
            $this->getRepository()->updateStatus($ids, $status);
        }
    }

    /**
     * Установить статус по заявке
     * @param int $id
     * @param string $status
     */
    public function setStatus(int $id, string $status)
    {
        $statusEntity = $this->planningStatusService->getStatusByAlias($status);
        $this->getRepository()->updateStatus($id, $statusEntity->getid());
    }

    /**
     * Удалить элемент заявки
     * @param PlanningItems $planningItem
     * @throws ORMException
     */
    public function deletePlanningItem(PlanningItems $planningItem): void
    {
        $this->planningItemService->remove($planningItem->getId());

        $planning = $planningItem->getPlanning();
        if (! $planning->hasItems()) {
            $this->remove($planning->getId());
        } else if ($planning->haveAllItemsBeenOrdered()) {
            $this->setStatus($planning->getId(), PlanningStatus::ALIAS_CLOSED);
        }
    }

    /**
     * Возвращает заказы по переданным item_id
     *
     * @param array $ids
     * @return mixed
     */
    protected function getByItemIds(array $ids)
    {
        if (empty($ids)) {
            return null;
        }
        return $this->getRepository()->getByItemIds($ids);
    }
}
