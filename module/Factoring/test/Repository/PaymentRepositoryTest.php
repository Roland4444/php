<?php

namespace FactoringTest\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use Factoring\Entity\Payment;
use Factoring\Repository\PaymentRepository;

class PaymentRepositoryTest extends BaseRepositoryTest
{
    protected function getRepository()
    {
        $metaData = new ClassMetadata(Payment::class);
        return  new PaymentRepository($this->getEntityManager(), $metaData);
    }

    public function testFindByPeriod()
    {
        $result = $this->getRepository()->findByPeriod('2000-01-01', '2019-01-01', null);
        $correctSql = 'SELECT p FROM Factoring\Entity\Payment p WHERE p.date >= :startdate and p.date <= :enddate ORDER BY p.date DESC';
        $this->assertEquals($correctSql, $result);
    }

    public function testGetSumFactoringWithoutBank()
    {
        $result = $this->getRepository()->getSumFactoring('2000-01-01');
        $correctSql = 'SELECT sum(p.money) FROM Factoring\Entity\Payment p WHERE p.date <= :enddate';
        $this->assertEquals($correctSql, $result);
    }

    public function testGetSumFactoringWithBank()
    {
        $result = $this->getRepository()->getSumFactoring('2000-01-01', 12);
        $correctSql = 'SELECT sum(p.money) FROM Factoring\Entity\Payment p WHERE p.date <= :enddate AND p.bank = :bankId';
        $this->assertEquals($correctSql, $result);
    }
}
