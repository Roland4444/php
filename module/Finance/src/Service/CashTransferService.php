<?php

namespace Finance\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Finance\Entity\Order;

class CashTransferService extends AbstractFinanceService
{
    protected $entity = '\Finance\Entity\CashTransfer';
    protected $order = ['date' => 'ASC'];
    protected $startdate;
    protected $enddate;
    protected $source;
    protected $dest;

    public function setDates($startdate, $enddate)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function setDest($dest)
    {
        $this->dest = $dest;
    }

    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,s,d')
            ->from($this->entity, 'r')
            ->join('r.source', 's')
            ->join('r.dest', 'd')
            ->where('r.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }

    public function getByParams(string $dateFrom, string $dateTo, int $source, int $dest): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.source', 's')
            ->join('r.dest', 'd')
            ->orderBy('r.date', 'DESC');
        if ($dateFrom && $dateTo) {
            $qb->andWhere("r.date >= :dateFrom and r.date <= :dateTo")
                ->setParameters([
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                ]);
        }
        if ($source) {
            $qb->andWhere('s.id = :source')
                ->setParameter('source', $source);
        }
        if ($dest) {
            $qb->andWhere('d.id = :dest')
                ->setParameter('dest', $dest);
        }
        return $qb->getQuery()->getResult();
    }

    public function getSumByPeriodGroupByBank(string $dateFrom, string $dateTo): array
    {
        $conn = $this->em->getConnection();
        $sql = <<<QUERY
            SELECT b.id,
                   s1.dest,
                   s2.source
            FROM bank_account b
            LEFT JOIN
              (SELECT ct.dest_id AS bankId,
                      sum(ct.money) AS dest
               FROM cash_transfer ct
               WHERE date>='{$dateFrom}'
                 AND date<='{$dateTo}'
               GROUP BY dest_id) s1 ON b.id=s1.bankId
            LEFT JOIN
              (SELECT ct.source_id AS bankId,
                      sum(ct.money) AS SOURCE
               FROM cash_transfer ct
               WHERE date>='{$dateFrom}'
                 AND date<='{$dateTo}'
               GROUP BY source_id) s2 ON b.id=s2.bankId
            WHERE b.closed=FALSE
QUERY;
        $rows = $conn->query($sql);
        $result = [];
        foreach ($rows->fetchAll() as $row) {
            $dest = $row['dest'] ? $row['dest'] : 0;
            $source = $row['source'] ? $row['source'] : 0;
            $result[$row['id']] = $dest - $source;
        }
        return $result;
    }

    /**
     * Создать перевод из платежки и сохранить
     * @param Order $order
     * @param array $params
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveFromOrder(Order $order, array $params = []): void
    {
        if (! empty($params['bank'])) {
            $order->setDest($params['bank']);
        }
        $transfer = new \Finance\Entity\CashTransfer();
        $transfer->setDate($order->getDate());
        $transfer->setMoney($order->getMoney());
        $transfer->setSource($order->getSource());
        $transfer->setDest($order->getDest());
        $transfer->setPaymentNumber($order->getNumber());
        $this->save($transfer);
    }
}
