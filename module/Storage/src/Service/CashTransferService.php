<?php
namespace Storage\Service;

use Application\Service\BaseService;
use Doctrine\ORM\Query;
use Storage\Entity\CashTransfer;

class CashTransferService extends BaseService
{
    protected $entity = CashTransfer::class;

    public function findBy(array $params, int $departmentId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,s,d')
            ->from($this->entity, 'r')
            ->join('r.source', 's')
            ->join('r.dest', 'd')
            ->andWhere('r.date >= :dateFrom and r.date<= :dateTo')
            ->setParameters([
                'dateFrom' => $params['startdate'],
                'dateTo' => $params['enddate']
            ])
            ->orderBy('r.date', 'DESC');

        if ($params['source']) {
            $qb->andWhere('s.id = :source')
                ->setParameter('source', $params['source']);
        }
        if ($params['dest']) {
            $qb->andWhere('d.id = :dest')
                ->setParameter('dest', $params['dest']);
        }
        if ($departmentId) {
            $qb->andWhere('s.id = :departmentId or d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param date $dateTo
     * @param int $departmentId
     * @return float
     */
    public function getBalance($dateTo, $departmentId)
    {
        if (! $dateTo || ! $departmentId) {
            return null;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->join('p.source', 's')
            ->join('p.dest', 'd')
            ->andWhere("p.date<='".$dateTo."'")
            ->andWhere("s.id = '".$departmentId."'");
        $out = $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);

        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->join('p.source', 's')
            ->join('p.dest', 'd')
            ->andWhere("p.date<='".$dateTo."'")
            ->andWhere("d.id = '".$departmentId."'");
        $in = $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);

        return $in - $out;
    }
}
