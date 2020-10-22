<?php
namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\Trader;

/**
 * Class TraderService
 * @package Reference\Service
 */
class TraderService extends BaseService
{
    /**
     * TraderService constructor.
     */
    public function __construct()
    {
        $this->setEntity(Trader::class);
    }

    private function clearDef()
    {
        $query = $this->em->createQuery("UPDATE ".$this->entity." s SET s.def = '0'");
        $query->execute();
    }

    public function save($row, $request = null)
    {
        if ($row->getDef()) {
            $this->clearDef();
        }
        $this->em->persist($row);
        $this->em->flush();
    }

    public function findByInn($inn)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->where('r.inn = '.$inn);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->addOrderBy('r.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Поиск данных с учетом фильтра
     *
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r')
            ->addOrderBy('r.name', 'ASC');

        if (! empty($params['name'])) {
            $qb->where('r.name like :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        if (! empty($params['inn'])) {
            $qb->andWhere('r.inn like :inn')
                ->setParameter('inn', '%' . $params['inn'] . '%');
        }

        if (! empty($params['traderParent'])) {
            $qb->andWhere('r.parent = :traderParent')
                ->setParameter('traderParent', $params['traderParent']);
        }

        return $qb->getQuery()->getResult();
    }
}
