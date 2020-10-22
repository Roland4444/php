<?php

namespace FactoringTest\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use Factoring\Entity\AssignmentDebt;
use Factoring\Repository\AssignmentDebtRepository;

class AssignmentDebtRepositoryTest extends BaseRepositoryTest
{
    protected function getRepository()
    {
        $metaData = new ClassMetadata(AssignmentDebt::class);
        return  new AssignmentDebtRepository($this->getEntityManager(), $metaData);
    }

    public function testFindByPeriod()
    {
        $result = $this->getRepository()->findByPeriod('2000-01-01', '2019-01-01');
        $correctSql = 'SELECT p FROM Factoring\Entity\AssignmentDebt p WHERE p.date >= :startdate and p.date <= :enddate ORDER BY p.date DESC';
        $this->assertEquals($correctSql, $result);
    }

    public function testGetSumFactoringWithoutBank()
    {
        $result = $this->getRepository()->getSumFactoring('2000-01-01');
        $correctSql = 'SELECT sum(p.money) FROM Factoring\Entity\AssignmentDebt p WHERE p.date <= :enddate';
        $this->assertEquals($correctSql, $result);
    }

    public function testGetSumFactoringWithBank()
    {
        $result = $this->getRepository()->getSumFactoring('2000-01-01', 12);
        $correctSql = 'SELECT sum(p.money) FROM Factoring\Entity\AssignmentDebt p WHERE p.date <= :enddate AND p.bank = :bankId';
        $this->assertEquals($correctSql, $result);
    }

    public function testGetSumGroupByTrader()
    {
        $result = $this->getRepository()->getSumGroupByTrader('2019-06-05');
        $correctSql = 'SELECT t.id, sum(p.money) as sum FROM Factoring\Entity\AssignmentDebt p INNER JOIN p.trader t WHERE p.date <= :dateTo GROUP BY p.trader ORDER BY p.date DESC';
        $this->assertEquals($correctSql, $result);
    }
}
