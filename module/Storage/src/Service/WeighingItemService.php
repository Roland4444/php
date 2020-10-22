<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Core\Traits\Base64ImageHandler;
use Reference\Service\MetalService;
use Storage\Entity\Weighing;
use Storage\Entity\WeighingItem;
use Storage\Repository\WeighingItemRepository;

/**
 * Class WeighingItemService
 * @package Storage\Service
 * @method  WeighingItemRepository getRepository() Метод класса AbstractService
 */
class WeighingItemService extends AbstractService
{
    use Base64ImageHandler;

    private $metalService;
    private $config;

    /**
     * @param MetalService $metalService
     */
    public function __construct($repository, $metalService, $config)
    {
        parent::__construct($repository);
        $this->metalService = $metalService;
        $this->config = $config;
    }

    public function fill($weighingItem): WeighingItem
    {
        $weighingItemRow = new WeighingItem();

        if (isset($weighingItem->id)) {
            $weighingItemRow = $this->getRepository()->find($weighingItem->id);
            $weighingItem->metalId = $weighingItem->metal->id;
            $weighingItemRow->setId($weighingItem->id);
            $weighingItemRow->setPrice($weighingItem->price);
        }

        $weighingItemRow->setBrutto($weighingItem->brutto);
        $weighingItemRow->setClogging($weighingItem->clogging);
        $weighingItemRow->setTare($weighingItem->tare);
        $weighingItemRow->setTrash($weighingItem->trash);
        $weighingItemRow->setWeighing($weighingItem->weighing);

        if (isset($weighingItem->photoPreview)) {
            $weighingItemRow->setPhotoPreview($weighingItem->photoPreview);
        }
        if (isset($weighingItem->photoFull)) {
            $weighingItemRow->setPhotoFull($weighingItem->photoFull);
        }

        $metal = $this->metalService->getReference($weighingItem->metalId);
        $weighingItemRow->setMetal($metal);

        return $weighingItemRow;
    }

    public function saveImagesForWeighing(Weighing $weighing)
    {
        foreach ($weighing->getWeighingItems() as $weighingItem) {
            $imageDir = $this->config['weighing_dir'] . $weighing->getDate() . '/';

            $fileNameForThumbnail = $imageDir . $weighingItem->getId() . '_' . $weighing->getDepartment()->getId() . '_thumbnail.jpg';
            $fileNameForFullImage = $imageDir . $weighingItem->getId() . '_' . $weighing->getDepartment()->getId() . '.jpg';

            $this->saveImage($fileNameForThumbnail, $imageDir, $weighingItem->getPhotoPreview());
            $this->saveImage($fileNameForFullImage, $imageDir, $weighingItem->getPhotoFull());
        }
    }

    /**
     * @param int $id - ИД Взвешивания
     * @param string $date - Дата взвешивания
     * @param int $departmentId - ид департамента
     */
    public function deleteImages(int $id, string $date, int $departmentId)
    {
        $items = $this->getRepository()->findBy([
            'weighing' => $id
        ]);
        foreach ($items as $weighingItem) {
            $this->deleteImage($weighingItem->getId(), $date, $departmentId);
        }
    }

    /**
     * @param int $weighingItemId - ИД элемента взвешивания
     * @param string $date - Дата взвешивания
     * @param int $departmentId - ИД департамента
     */
    public function deleteImage(int $weighingItemId, string $date, int $departmentId)
    {
        $imageDir = $this->config['weighing_dir'] . $date . '/';
        $fileNameForThumbnail = $imageDir . $weighingItemId . '_' . $departmentId . '_thumbnail.jpg';
        $fileNameForFullImage = $imageDir . $weighingItemId . '_' . $departmentId . '.jpg';
        unlink($fileNameForThumbnail);
        unlink($fileNameForFullImage);
    }

    /**
     * Получить элементы взвешиваний, групированные по наименованию металла
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $departmentId
     * @return array
     */
    public function getGrouppedByMetal(string $dateFrom, string $dateTo, int $departmentId): array
    {
        return array_map(function ($item) {
            return [
                'mass' => $item['mass'],
                'avg_price' => empty($item['avg_price']) ? 0 : $item['avg_price'],
                'name' => $item['name'],
            ];
        }, $this->getRepository()->getGrouppedByMetal($dateFrom, $dateTo, $departmentId));
    }
}
