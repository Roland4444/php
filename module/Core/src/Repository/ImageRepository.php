<?php

namespace Core\Repository;

use Core\Entity\Image;

class ImageRepository extends AbstractRepository
{
    public function deleteImages(string $entity, int $entity_id, string $dir): void
    {
        $images = $this->findBy([
            'entity' => $entity,
            'entity_id' => $entity_id
        ]);

        /** @var Image $image */
        foreach ($images as $image) {
            $filename = $dir . '/' . $image->getFilename();
            if (file_exists($filename)) {
                unlink($filename);
            }
            $this->getEntityManager()->remove($image);
        }
        $this->getEntityManager()->flush();
    }
}
