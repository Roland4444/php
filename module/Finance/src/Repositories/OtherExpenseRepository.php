<?php
namespace Finance\Repositories;

use Doctrine\ORM\EntityRepository;

class OtherExpenseRepository extends EntityRepository
{
    /**
     * Получение списка платежей по указанным инн поставщиков
     *
     * @param $dataFrom
     * @param $dateTo
     * @param $innList
     * @param null $name
     * @param null $categoryAlias
     * @return mixed
     */
    public function getByInn($dataFrom, $dateTo, $innList, $name = null, $categoryAlias = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r, t')
            ->from($this->getClassName(), 'r')
            ->leftJoin('r.order', 't')
            ->join('r.bank', 'b')
            ->join('r.category', 'c')
            ->where("r.date >= :dataFrom and r.date <= :dateTo")
            ->andWhere("r.inn IN (:innListString)")
            ->andWhere('b.cash=0')
            ->orderBy('r.date', 'DESC')
            ->setParameters([
                'dataFrom' => $dataFrom,
                'dateTo' => $dateTo,
                'innListString' => $innList,
            ]);

        if ($categoryAlias) {
            $qb->andWhere('c.alias = :category')
                ->setParameter('category', $categoryAlias);
        }
        if (! empty($name)) {
            $qb->andWhere("r.comment LIKE :name")
                ->setParameter('name', '%'.$name.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
