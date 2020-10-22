<?php

namespace FactoringTest\Entity;

use Exception;
use Factoring\Entity\Payment;
use Factoring\Entity\Provider;
use Finance\Entity\BankAccount;
use PHPUnit\Framework\TestCase;
use Zend\InputFilter\InputFilter;

class PaymentTest extends TestCase
{
    /**
     * @var Payment
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Payment();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Payment::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate(date('Y-m-d'));
        $entity->setMoney(500);
        $entity->setProvider(new Provider());
        $entity->setBank(new BankAccount());

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertEquals(500, $entity->getMoney());
        $this->assertInstanceOf(Provider::class, $entity->getProvider());
        $this->assertInstanceOf(BankAccount::class, $entity->getBank());
    }

    public function testSetInputFilter()
    {
        $this->expectException(Exception::class);
        $this->entity->setInputFilter(new InputFilter);
    }

    public function testGetInputFilter()
    {
        $filter = $this->entity->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $filter);
    }
}
