<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Core\Service\ImageService;
use Exception;
use Storage\Entity\Weighing;
use Storage\Entity\WeighingItem;
use Storage\Repository\Interfaces\WeighingRepositoryInterface;

class WeighingService extends AbstractService
{
    private $departmentService;
    private $weighingItemService;
    private $customerService;
    private $imageService;
    private $weighingDir;

    public function __construct(
        WeighingRepositoryInterface $repository,
        $weighingItemService,
        $departmentService,
        $customerService,
        ImageService $imageService,
        $weighingDir
    ) {
        parent::__construct($repository);
        $this->weighingItemService = $weighingItemService;
        $this->departmentService = $departmentService;
        $this->customerService = $customerService;
        $this->imageService = $imageService;
        $this->weighingDir = $weighingDir;
    }

    /**
     * Сохранить взвешивание
     * @param $data
     * @throws \Exception
     */
    public function store($data): void
    {
        $weighingDecodedData = json_decode($data);

        $weighingEntity = $this->fill($weighingDecodedData);

        $isWeighingExists = $this->getRepository()->getWeighingByExportIdDepartmentDate($weighingDecodedData);

        if (! $isWeighingExists) {
            $this->getRepository()->save($weighingEntity);
            $this->weighingItemService->saveImagesForWeighing($weighingEntity);
        }
    }

    public function update(Weighing $weighingToUpdate): Weighing
    {
        $this->getRepository()->save($weighingToUpdate);
        return $weighingToUpdate;
    }

    /**
     * Поиск данных с учетом фильтра
     *
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        return $this->getRepository()->getTableList($params);
    }

    /**
     * Получить элементы взвешивания сгрупированные по металлу
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $departmentId
     * @return array
     */
    public function getGrouppedByMetal(string $dateFrom, string $dateTo, int $departmentId): array
    {
        return $this->weighingItemService->getGrouppedByMetal($dateFrom, $dateTo, $departmentId);
    }

    /**
     * Получить полноразмерное фото изображения
     * @param int $weighingItemId - ИД элемента взвешивания
     * @param string $type - preview или full
     * @return void
     */
    public function getWeighingItemImage(int $weighingItemId, string $type)
    {
        /** @var WeighingItem $weighingItem */
        $weighingItem = $this->weighingItemService->find($weighingItemId);
        $fileDir = $this->weighingDir . $weighingItem->getWeighing()->getDate();
        $fileName = $weighingItemId . '_' . $weighingItem->getWeighing()->getDepartment()->getId() . $type. '.jpg';

        $this->imageService->printImage($fileDir, $fileName);
    }

    public function delete(int $id)
    {
        $weighing = $this->getRepository()->find($id);
        $this->weighingItemService->deleteImages($id, $weighing->getDate(), $weighing->getDepartment()->getId());
        $this->remove($id);
    }

    public function deleteItem(int $id)
    {
        $weighingItem = $this->weighingItemService->getReference($id);
        $weighingItemCount = count($weighingItem->getWeighing()->getWeighingItems());

        $this->weighingItemService->remove($id);

        // Удалить картинку
        $this->weighingItemService->deleteImage(
            $id,
            $weighingItem->getWeighing()->getDate(),
            $weighingItem->getWeighing()->getDepartment()->getId()
        );

        if ($weighingItemCount == 1) {
            $this->delete($weighingItem->getWeighing()->getId());
        }
    }

    /**
     * Получить взвешивания группированные по поставщик, металлу, цене за заданную дату и по подразделению
     * @param string $date
     * @param int $departmentId
     * @return mixed
     * @throws Exception
     */
    public function getAggregateWeighings(string $date, int $departmentId)
    {
        $groupedWeighingList = [];

        $weighings = $this->getRepository()->getByDateAndDepartment($date, $departmentId);

        /** @var Weighing $weighing */
        foreach ($weighings as $weighing) {

            /** @var WeighingItem $weighingItem */
            foreach ($weighing->getWeighingItems() as $weighingItem) {
                if ($weighing->getCustomer() === null) {
                    throw new Exception('Не для всех взвешиваний за данный день указаны поля цена или поставщик');
                }

                $customerId = $weighing->getCustomer()->getId();
                $price = 'p'.$weighingItem->getPrice();
                $metalId = $weighingItem->getMetal()->getId();

                if (! isset($groupedWeighingList[$customerId][$metalId][$price])) {
                    $groupedWeighingList[$customerId][$metalId][$price] = [
                        'customer' => $weighing->getCustomer(),
                        'department' => $weighing->getDepartment(),
                        'metal' => $weighingItem->getMetal(),
                        'price' => $weighingItem->getPrice(),
                        'mass' => $weighingItem->getMass()
                    ];
                } else {
                    $groupedWeighingList[$customerId][$metalId][$price]['mass'] += $weighingItem->getMass();
                }
            }
        }
        return $groupedWeighingList;
    }

    public function fill($weighingDecodedData): Weighing
    {
        $weighingEntity = new Weighing();

        if (isset($weighingDecodedData->customer)) {
            $weighingEntity = $this->getRepository()->find($weighingDecodedData->id);
            $weighingEntity->setCustomer(
                $this->customerService->getReference($weighingDecodedData->customer)
            );
        }

        // Так как при создании взвешивания id - является export_id - его нужно оставить прежним при обновлении
        if (! isset($weighingDecodedData->customer)) {
            $weighingEntity->setExportId($weighingDecodedData->id);
        }

        $weighingEntity->setDepartment(
            $this->departmentService->getReference($weighingDecodedData->departmentId)
        );

        $weighingEntity->setWaybill($weighingDecodedData->waybill);
        $weighingEntity->setDate($weighingDecodedData->date);
        $weighingEntity->setTime($weighingDecodedData->time);
        $weighingEntity->setComment($weighingDecodedData->comment);

        foreach ($weighingDecodedData->weighings as $weighingItem) {
            $weighingItem->weighing = $weighingEntity;
            $weighingItemEntity = $this->weighingItemService->fill($weighingItem);
            $weighingEntity->addItem($weighingItemEntity);
        }

        return $weighingEntity;
    }
}
