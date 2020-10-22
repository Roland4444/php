<?php

namespace Core\Service;

use Core\Entity\Image;
use Core\Repository\ImageRepository;

class ImageService extends AbstractService
{
    private $uploadsDir;

    /**
     * {@inheritDoc}
     */
    public function __construct(ImageRepository $repository, $uploadsDir)
    {
        parent::__construct($repository);
        $this->uploadsDir = $uploadsDir;
    }

    public function getImages(string $entity, int $entityId): array
    {
        return $this->getRepository()->findBy([
            'entity' => $entity,
            'entity_id' => $entityId
        ]);
    }

    public function saveImages(array $files, string $entity, int $entityId): void
    {
        $uploadsDir = $this->uploadsDir;

        $imageDir = $this->getImageDir($entity);
        if (count($files) > 0 && ! file_exists($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        $this->deleteImages($entity, $entityId);

        foreach ($files as $key => $file) {
            $image = new Image();
            $image->setEntity($entity);
            $image->setEntityId($entityId);
            $image->setFilename(md5(date('YmdHis').$entity.$entityId).$key.'.png');
            parent::save($image);

            move_uploaded_file($file["tmp_name"], $uploadsDir . $this->getPath($image));
        }
    }

    private function getImageDir(string $entity): string
    {
        $uploadsDir = $this->uploadsDir;
        return $uploadsDir . $this->getShortEntityName($entity);
    }

    public function deleteImages(string $entity, int $entity_id): void
    {
        $imageDir = $this->getImageDir($entity);
        $this->getRepository()->deleteImages($entity, $entity_id, $imageDir);
    }

    public function getPath(Image $image): string
    {
        return $this->getShortEntityName($image->getEntity()) . '/' . $image->getFilename();
    }

    private function getShortEntityName(string $entity): string
    {
        return strtolower(substr($entity, strrpos($entity, '\\') + 1));
    }

    public function printImage($fileDir, $fileName)
    {
        header('Content-Type: image/jpeg');
        readfile($fileDir . '/' . $fileName);
        exit();
    }
}
