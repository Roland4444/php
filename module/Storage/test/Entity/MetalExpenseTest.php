<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Customer;
use Reference\Entity\Department;
use Storage\Entity\MetalExpense;
use Storage\Entity\PurchaseDeal;
use Zend\InputFilter\InputFilter;

class MetalExpenseTest extends TestCase
{
    /**
     * @var MetalExpense
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new MetalExpense();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(MetalExpense::class, $this->entity);
    }

    public function testGettersDefaultValues()
    {
        $entity = $this->entity;

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertNull($entity->getMoney());
        $this->assertNull($entity->getFormal());
        $this->assertNull($entity->getCustomer());
        $this->assertNull($entity->getDepartment());
        $this->assertNull($entity->getDeal());
        $this->assertNull($entity->getDiamond());
    }

    public function testPlainSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate('2000-01-01');
        $entity->setMoney(500);
        $entity->setFormal(true);
        $entity->setCustomer(new Customer());
        $entity->setDepartment(new Department());
        $entity->setDeal(new PurchaseDeal());
        $entity->setDiamond(true);

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('2000-01-01', $entity->getDate());
        $this->assertEquals(500, $entity->getMoney());
        $this->assertTrue($entity->getFormal());
        $this->assertInstanceOf(Customer::class, $entity->getCustomer());
        $this->assertInstanceOf(Department::class, $entity->getDepartment());
        $this->assertInstanceOf(PurchaseDeal::class, $entity->getDeal());
        $this->assertTrue($entity->getDiamond());
    }

    public function testSetInputFilter()
    {
        $this->expectException(Exception::class);
        $this->entity->setInputFilter(new InputFilter);
    }

    public function testFetInputFilter()
    {
        $filter = $this->entity->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $filter);
    }
}
