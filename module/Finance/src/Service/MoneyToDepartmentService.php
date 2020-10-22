<?php

namespace Finance\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Finance\Entity\MoneyToDepartment;

class MoneyToDepartmentService
{
    private string $entity = MoneyToDepartment::class;

    private EntityManager $entityManager;
    private BankService $bankAccountService;

    public function __construct($entityManager, $bankAccountService)
    {
        $this->entityManager = $entityManager;
        $this->bankAccountService = $bankAccountService;
    }

    public function find($id)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r,d,b')
            ->from($this->entity, 'r')
            ->join('r.department', 'd')
            ->join('r.bank', 'b')
            ->where('r.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getSingleResult();
    }

    public function findByParams($dateFrom, $dateTo, $departmentId = null, $bankId = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r,d')
            ->from($this->entity, 'r')
            ->join('r.department', 'd')
            ->join('r.bank', 'b')
            ->where('r.date >= :dateFrom and r.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ])
            ->orderBy('r.date', 'DESC');
        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }
        if ($bankId) {
            $qb->andWhere('b.id = :bankId')
                ->setParameter('bankId', $bankId);
        }
        return $qb->getQuery()->getResult();
    }

    public function save($row)
    {
        $this->entityManager->persist($row);
        $this->entityManager->flush();
        return true;
    }

    public function remove($id): void
    {
        $row = $this->entityManager->find($this->entity, $id);
        $this->entityManager->remove($row);
        $this->entityManager->flush();
    }

    /**
     * Get total sum by department
     * @param $dateFrom
     * @param $dateTo
     * @param $departmentId
     * @param null $bankId
     * @return mixed
     */
    public function getTotalSumByDepartment($dateFrom, $dateTo, $departmentId = null, $bankId = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('sum(p.money)')
            ->from($this->entity, 'p')
            ->join('p.department', 'd')
            ->join('p.bank', 'b')
            ->where('p.date >= :dateFrom and p.date <= :dateTo')
            ->setParameters([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]);
        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }
        if ($bankId) {
            $qb->andWhere('b.id = :bankId')
                ->setParameter('bankId', $bankId);
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function getSumByPeriodAndBank(string $dateFrom, string $dateTo): ?float
    {
        $qb = $this->entityManager->createQueryBuilder();
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
        $conn = $this->entityManager->getConnection();
        $sql = <<<QUERY
            SELECT b.id,
                   s1.sum
            FROM bank_account b
            LEFT JOIN
              (SELECT r.bank_id AS bankId, sum(r.money) AS sum
               FROM money_to_department r
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

    /**
     * @param array $expenseData
     */
    public function createOrUpdateFromDiamond(array $expenseData): void
    {
        $row = $this->findFromDiamond($expenseData['date'], $expenseData['department']->getId());
        if (empty($row)) {
            $row = new MoneyToDepartment();
            $row->setMoney($expenseData['money']);
            $row->setDate($expenseData['date']);
            $row->setDepartment($expenseData['department']);
            $row->setVerified(false);
            $bankAccount = $this->bankAccountService->findDiamond();
            $row->setBank($bankAccount);
        } else {
            $sum = bcadd($row->getMoney(), $expenseData['money'], 16);
            $row->setMoney($sum);
        }
        $this->save($row);
    }

    /**
     * @param $date
     * @param $departmentId
     * @return MoneyToDepartment|null
     */
    private function findFromDiamond($date, $departmentId): ?MoneyToDepartment
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.bank', 'b')
            ->where('r.department = :department')
            ->setParameter('department', $departmentId)
            ->andWhere('r.date = :date')
            ->setParameter('date', $date)
            ->andWhere('b.diamond = 1');

        $result = $qb->getQuery()->getResult();
        return empty($result[0]) ? null : $result[0];
    }
}
