<?php

namespace Finance\Service;

use Core\Utils\Conditions;
use Core\Utils\Options;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Finance\Entity\Order;
use Finance\Entity\OtherReceipts;
use Zend\Http\Request;

class OtherReceiptService extends AbstractFinanceService
{
    protected $entity = '\Finance\Entity\OtherReceipts';
    protected $order = ['date' => 'ASC'];

    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->where('r.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }

    public function findByParams(string $dateFrom, string $dateTo, ?int $bankId, ?string $comment)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->orderBy('r.date', 'DESC');
        if ($dateFrom && $dateTo) {
            $qb->andWhere("r.date >= :dateFrom and r.date <= :dateTo")
                ->setParameters([
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                ]);
        }
        if ($bankId) {
            $qb->andWhere('b.id = :bankId')
                ->setParameter('bankId', $bankId);
        }
        if ($comment) {
            $qb->andWhere("r.comment like :comment")
                ->setParameter('comment', '%' . $comment . '%');
        }
        return $qb->getQuery()->getResult();
    }

    public function getSumByPeriodAndBank(string $dateFrom, string $dateTo): ?float
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.money)')
            ->from($this->entity, 'r')
            ->where('r.date >= :dateFrom and r.date <= :dateTo')
            ->andWhere(Conditions::jsonNotContains('r', Options::OVERDRAFT))
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
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
              (SELECT r.bank_account_id AS bankId, sum(r.money) AS sum
               FROM main_receipts r
               WHERE date>='{$dateFrom}'
                 AND date<='{$dateTo}'
               GROUP BY r.bank_account_id) s1 ON b.id=s1.bankId
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveFromOrder(Order $order): void
    {
        $options = [];
        $sberbankInn = '7707083893';
        if (trim($order->getSourceInn()) == $sberbankInn) {
            $options [] = Options::OVERDRAFT;
        }
        $otherReceipt = new \Finance\Entity\OtherReceipts();
        $otherReceipt->setDate($order->getDate());
        $otherReceipt->setBank($order->getDest());
        $otherReceipt->setPaymentNumber($order->getNumber());
        $otherReceipt->setMoney($order->getMoney());
        $otherReceipt->setComment($order->getComment());
        $otherReceipt->setInn($order->getSourceInn());
        $otherReceipt->setSender($order->getSender());

        if (! empty($options)) {
            foreach ($options as $option) {
                $otherReceipt->setOption($option, true);
            }
        }

        $this->save($otherReceipt);
    }

    /**
     * @param OtherReceipts $row
     * @param Request $request
     * @return mixed|void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($row, $request = null)
    {
        if ($request !== null) {
            $row->setOption(Options::OVERDRAFT, $request->getPost(Options::OVERDRAFT));
        }
        parent::save($row);
    }
}
