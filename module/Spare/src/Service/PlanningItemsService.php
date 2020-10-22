<?php
namespace Spare\Service;

use Application\Service\BaseService;
use Spare\Entity\PlanningItems;

class PlanningItemsService extends BaseService
{
    protected $entity = PlanningItems::class;
    protected $order = ['id' => 'DESC'];

    /**
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Удаление позиций заявки
     *
     * @param \Doctrine\ORM\PersistentCollection $items Список позиций заказа
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeItems($items)
    {
        if (! empty($items)) {
            foreach ($items as $item) {
                $this->em->remove($item);
            }
            $this->em->flush();
        }
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
            ->from($this->entity, 'r')
            ->where('r.id IN (' . $ids . ')');
        return $qb->getQuery()->getResult();
    }
}
