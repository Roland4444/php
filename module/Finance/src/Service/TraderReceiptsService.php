<?php

namespace Finance\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Finance\Entity\Order;
use Finance\Entity\TraderReceipts;

class TraderReceiptsService extends AbstractFinanceService
{
    protected $entity = TraderReceipts::class;
    protected $order = ['date' => 'ASC'];

    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b,t')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->join('r.trader', 't')
            ->where('r.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }

    public function findBy(string $dateFrom, string $dateTo, int $bank, int $trader, string $type)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b,t')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->join('r.trader', 't')
            ->orderBy('r.date', 'DESC')
            ->andWhere('r.date >= :dateFrom and r.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
        if ($trader > 0) {
            $qb->andWhere('t.id = :traderId')
                ->setParameter('traderId', $trader);
        }
        if ($bank > 0) {
            $qb->andWhere('b.id = :bankId')
                ->setParameter('bankId', $bank);
        }
        if (! empty($type)) {
            $qb->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }
        return $qb->getQuery()->getResult();
    }

    public function getSumByPeriodAndBank(string $dateFrom, string $dateTo): ?float
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.money)')
            ->from($this->entity, 'r')
            ->where('r.date >=:dateFrom and r.date<= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSumByPeriodGroupByBank(string $dateFrom, string $dateTo): array
    {
        $conn = $this->em->getConnection();
        $sql = <<<QUERY
            SELECT b.id,
                   s1.sum
            FROM bank_account b
            LEFT JOIN
              (SELECT tr.bank_account_id AS bankId, sum(tr.money) AS sum
               FROM main_receipts_trader tr
               WHERE date>='{$dateFrom}'
                 AND date<='{$dateTo}'
               GROUP BY tr.bank_account_id) s1 ON b.id=s1.bankId
            WHERE b.closed=FALSE
QUERY;
        $rows = $conn->query($sql);
        $result = [];
        foreach ($rows->fetchAll() as $row) {
            $result[$row['id']] = $row['sum'] ? $row['sum'] : 0;
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
    public function saveFromOrder(Order $order, array $params): void
    {
        $trader = $params['trader'];
        $traderReceipt = new TraderReceipts();
        $traderReceipt->setDate($order->getDate());
        $traderReceipt->setPaymentNumber($order->getNumber());
        $traderReceipt->setMoney($order->getMoney());
        $traderReceipt->setBank($order->getDest());
        $traderReceipt->setTrader($trader);
        $type = $trader->getParent()->getName() === 'Цветмет' ? 'color' : 'black';
        $traderReceipt->setType($type);
        $this->save($traderReceipt);
    }
}
