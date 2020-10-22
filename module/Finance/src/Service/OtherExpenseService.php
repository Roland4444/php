<?php

namespace Finance\Service;

use Core\Entity\ExpenseDay;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Finance\Entity\BankAccount;
use Finance\Entity\Order;
use Finance\Repositories\OtherExpenseRepository;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\I18n\View\Helper\DateFormat;

class OtherExpenseService extends AbstractFinanceService
{
    const COMMENT_RNKO = 'Комиссия рнко';
    protected $entity = '\Finance\Entity\OtherExpense';
    protected $order = ['date' => 'ASC'];
    protected $startdate;
    protected $enddate;
    protected $customer;
    protected $category;
    protected $comment;
    protected $bank;

    /**
     * @return OtherExpenseRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository($this->entity);
    }

    protected $filterParams = [
        'startdate',
        'enddate',
        'bank',
        'category',
        'comment',
    ];

    public function setDates($startdate, $enddate)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getByCategoryId($categoryId)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('r')
            ->from($this->entity, 'r')
            ->addOrderBy('r.date', 'DESC')
            ->where('r.category = :category')
            ->setParameter('category', $categoryId);

        if (! empty($this->startdate) && ! empty($this->enddate)) {
            $qb->andWhere("r.date >='" . $this->startdate . "' and r.date<='" . $this->enddate . "'");
        }
        if (! empty($this->bank)) {
            $qb->andWhere("r.bank =" . (int)$this->bank);
        }
        if (! empty($this->comment)) {
            $qb->andWhere("r.comment LIKE '%" . $this->comment . "%'");
        }

        return $qb->getQuery()->getResult();
    }

    public function getSumByCategoryId($categoryId)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('SUM(r.money)')
            ->from($this->entity, 'r')
            ->addOrderBy('r.date', 'DESC')
            ->where('r.category = :category')
            ->setParameter('category', $categoryId);

        if (! empty($this->startdate) && ! empty($this->enddate)) {
            $qb->andWhere("r.date >='" . $this->startdate . "' and r.date<='" . $this->enddate . "'");
        }
        if (! empty($this->bank)) {
            $qb->andWhere("r.bank =" . (int)$this->bank);
        }
        if (! empty($this->comment)) {
            $qb->andWhere("r.comment LIKE '%" . $this->comment . "%'");
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param $id
     * @return \Finance\Entity\OtherExpense
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b,c')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->join('r.category', 'c')
            ->where('r.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }


    public function findAll(bool $withoutCash = false)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r,b,c')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->join('r.category', 'c')
            ->orderBy('r.date', 'DESC');
        if ($this->startdate && $this->enddate) {
            $qb->andWhere("r.date >= '" . $this->startdate . "' and r.date<='" . $this->enddate . "'");
        }
        if ($this->bank) {
            $qb->andWhere("b.id = " . $this->bank);
        }
        if ($this->category) {
            $qb->andWhere("c.id = " . $this->category);
        }
        if ($this->comment) {
            $qb->andWhere("r.comment like '%" . $this->comment . "%'");
        }
        if ($withoutCash) {
            $qb->andWhere("b.cash = 0");
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * Создаем на основе данных из базы массив дней с расходами
     * @param array $data
     * @return array
     */
    public function makeArrayOfDay(array $data)
    {
        $res = [];
        $sum = 0;
        foreach ($data as $row) {
            if (! isset($res[$row->getDate()])) {
                $day = new ExpenseDay($row->getDate());
                $res[$row->getDate()] = $day;
            }
            $res[$row->getDate()]->addExpense($row);
            $sum += $row->getMoney();
        }

        $currencyFormatter = new CurrencyFormat();
        $dateFormatter = new DateFormat();
        $json = [];
        foreach ($res as $day) {
            $expenses = [];
            foreach ($day->getExpenses() as $exp) {
                $expenses[] = [
                    'id' => $exp->getId(),
                    'date' => $exp->getDate(),
                    'realdate' => $exp->getRealDate(),
                    'category' => $exp->getCategory(),
                    'money' => $exp->getMoney(),
                    'moneyFormat' => $currencyFormatter($exp->getMoney(), 'RUR'),
                    'recipient' => $exp->getRecipient(),
                    'bank' => $exp->getBank(),
                    'comment' => $exp->getComment(),
                    'inn' => $exp->getInn(),
                ];
            }
            $json[] = [
                'date' => $dateFormatter(\DateTime::createFromFormat('Y-m-d', $day->getDate()), \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'ru_RU'),
                'expenses' => $expenses,
                'sum' => $currencyFormatter($day->getSum(), 'RUR'),
            ];
        }

        return [
            'rows' => $json,
            'sum' => $currencyFormatter($sum, 'RUR')
        ];
    }

    public function getSumByPeriodAndBank(string $dateFrom, string $dateTo): ?float
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->where('p.date >= :dateFrom and p.date <= :dateTo')
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
              (SELECT r.bank_id AS bankId, sum(r.money) AS sum
               FROM main_other_expenses r
               WHERE date>='{$dateFrom}'
                 AND date<='{$dateTo}'
               GROUP BY r.bank_id) s1 ON b.id=s1.bankId
            WHERE b.closed=FALSE
QUERY;
        $rows = $conn->query($sql);
        $result = [];
        foreach ($rows->fetchAll() as $row) {
            $result[$row['id']] = $row['sum'] ? $row['sum'] : 0;
        }
        return $result;
    }

    public function getSumByInn($inn, $dateFrom, $dateTo)
    {
        if ($inn != 0) {
            $qb = $this->em->createQueryBuilder();
            $qb->select('sum(p.money)')
                ->from($this->entity, 'p')
                ->join('p.bank', 'b')
                ->join('p.category', 'c')
                ->where("p.inn = '" . $inn . "'");
            if ($dateFrom && $dateTo) {
                $qb->andWhere("p.date >= '" . $dateFrom . "' and p.date<='" . $dateTo . "'");
            }
            return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
        }
        return 0;
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return mixed
     */
    public function getSumByCategory(string $dateFrom, string $dateTo)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(r.money) as money, c.name as category, g.name as gr, g.id as gid, g.sortOrder as sort')
            ->from($this->entity, 'r')
            ->join('r.category', 'c')
            ->join('c.group', 'g')
            ->groupBy('r.category')
            ->andWhere("r.realdate >= :dateFrom and r.realdate <= :dateTo")
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ])
            ->orderBy('c.name');
        return $qb->getQuery()->getResult();
    }

    protected function hasDuplicate($row)
    {
        if ($row->getPaymentNumber()) {
            $num = trim($row->getPaymentNumber());
            $qb = $this->em->createQueryBuilder();
            $qb->select('r')
                ->from($this->entity, 'r')
                ->leftJoin('r.bank', 's')
                ->where("r.payment_number = '" . $num . "'")
                ->andWhere("r.date = '" . $row->getDate() . "'")
                ->andWhere("r.money = '" . $row->getMoney() . "'")
                ->andWhere("s.id = " . $row->getBank()->getId());
            return count($qb->getQuery()->getResult()) > 0;
        }
        return false;
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
        $otherExpense = new \Finance\Entity\OtherExpense();
        $otherExpense->setDate($order->getDate());
        $otherExpense->setBank($order->getSource());
        $otherExpense->setPaymentNumber($order->getNumber());
        $otherExpense->setComment($order->getComment());
        $otherExpense->setMoney($order->getMoney());
        $otherExpense->setCategory($params['category']);
        $otherExpense->setInn($order->getDestInn());
        $otherExpense->setRecipient($order->getRecipient());
        $this->save($otherExpense);
    }

    /**
     * Возвращает сущность управленческого расхода по дате указанной дате
     *
     * @param $date
     * @return \Finance\Entity\OtherExpense|null
     */
    public function getDiamondExpense($date)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.bank', 's')
            ->where("s.alias = '" . BankAccount::ALIAS_RNKO . "' AND r.date = '$date'");
        $result = $qb->getQuery()->getResult();
        return empty($result[0]) ? null : $result[0];
    }

    /**
     * Получение списка платежей по указанным инн поставщиков
     *
     * @param $dataFrom
     * @param $dateTo
     * @param $innList
     * @param null $name
     * @param null $categoryAlias
     * @return array
     */
    public function getByInn($dataFrom, $dateTo, $innList, $name = null, $categoryAlias = null)
    {
        return $this->getRepository()->getByInn($dataFrom, $dateTo, $innList, $name, $categoryAlias);
    }
}
