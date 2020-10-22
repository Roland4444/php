<?php
namespace Spare\Service;

use Application\Service\BaseService;
use Core\Entity\Image;
use Core\Service\ImageService;
use Spare\Entity\Spare;

/**
 * Class SpareService
 * @package Reference\SpareService
 */
class SpareService extends BaseService
{
    private $repository;
    private $imageService;

    /**
     * SpareService constructor.
     *
     * @param $repository
     * @param $em
     * @param ImageService $imageService
     */
    public function __construct($repository, $em, ImageService $imageService)
    {
        $this->repository = $repository;
        $this->setEntity(Spare::class);
        $this->em = $em;
        $this->imageService = $imageService;
    }

    private function getRepository()
    {
        return $this->repository;
    }

    /**
     * Поиск всех записей из таблицы запчасти
     *
     * @return mixed
     */
    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->addOrderBy('r.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Получение записей по указанным id
     *
     * @param array $ids
     * @return mixed
     */
    public function findByIds($ids)
    {
        $ids = implode(',', $ids);
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r', 'r.id')
            ->where('r.id IN (' . $ids . ')');
        return $qb->getQuery()->getResult();
    }

    /**
     * Формирмирование json для передачи данных на фронт
     *
     * @return false|string
     */
    public function getSpareJson()
    {
        $spares = $this->findAll();
        if (empty($spares)) {
            return '[]';
        }

        $jsonSpare = [];

        foreach ($spares as $spare) {
            $jsonSpare[$spare->getId()] = [
                'value' => $spare->getId(),
                'text' => $spare->getName(),
                'isComposite' => (boolean)$spare->getIsComposite(),
                'units' => $spare->getUnits()
            ];
        }

        return json_encode($jsonSpare, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Поиск данных с учетом фильтра
     *
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        $res = $this->repository->findRawWithImages($params);
        $spares = [];
        foreach ($res as $row) {
            if (empty($spares[$row['id']])) {
                $spare = new Spare();
                $spare->setId($row['id']);
                $spare->setComment($row['comment']);
                $spare->setIsComposite($row['is_composite']);
                $spare->setName($row['name']);
                $spare->setUnits($row['units']);
                if (! empty($row['filename'])) {
                    $image = new Image();
                    $image->setFilename($row['filename']);
                    $spare->addImage($image);
                }
                $spares[$row['id']] = $spare;
            } else {
                if (! empty($row['filename'])) {
                    $image = new Image();
                    $image->setFilename($row['filename']);
                    $spares[$row['id']]->addImage($image);
                }
            }
        }
        return $spares;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getReference($id)
    {
        return $this->getRepository()->getReference($id);
    }

    /**
     * {@inheritDoc
     */
    public function save($row, $request = null)
    {
        parent::save($row, $request);
        $files = $request->getFiles();
        if (count($files) == 0) {
            $files['images'] = [];
        }
        $this->imageService->saveImages($files['images'], Spare::class, $row->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function remove($id)
    {
        $entity = $this->repository->getClassName();
        $this->imageService->deleteImages($entity, $id);
        parent::remove($id);
    }
}
