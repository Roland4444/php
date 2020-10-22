<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Customer;
use Reference\Entity\Department;
use Reference\Entity\Metal;
use Storage\Entity\Purchase;
use Storage\Entity\PurchaseDeal;
use Zend\InputFilter\InputFilter;

class PurchaseTest extends TestCase
{
    /**
     * @var Purchase
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Purchase();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Purchase::class, $this->entity);
    }

    public function testGettersDefaultValues()
    {
        $entity = $this->entity;

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertNull($entity->getWeight());
        $this->assertNull($entity->getCost());
        $this->assertNull($entity->getFormal());
        $this->assertNull($entity->getMetal());
        $this->assertNull($entity->getDepartment());
        $this->assertNull($entity->getCustomer());
        $this->assertNull($entity->getDeal());
        $this->assertEquals(date('Y-m-d H:i:s'), $entity->getCreatedAt());
    }

    public function testPlainSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate('2015-01-01');
        $entity->setWeight(1000);
        $entity->setCost(12.5);
        $entity->setFormal(11.5);
        $entity->setMetal(new Metal());
        $entity->setDepartment(new Department());
        $entity->setCustomer(new Customer());
        $entity->setDeal(new PurchaseDeal());

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('2015-01-01', $entity->getDate());
        $this->assertEquals(1000, $entity->getWeight());
        $this->assertEquals(12.5, $entity->getCost());
        $this->assertEquals(11.5, $entity->getFormal());
        $this->assertInstanceOf(Metal::class, $entity->getMetal());
        $this->assertInstanceOf(Department::class, $entity->getDepartment());
        $this->assertInstanceOf(Customer::class, $entity->getCustomer());
        $this->assertInstanceOf(PurchaseDeal::class, $entity->getDeal());
    }

    public function testGetSum()
    {
        $entity = $this->entity;
        $entity->setWeight(1000);
        $entity->setCost(12.5);

        $this->assertEquals(1000, $entity->getWeight());
        $this->assertEquals(12.5, $entity->getCost());
    }

    public function testGetSumFormal()
    {
        $entity = $this->entity;
        $entity->setWeight(100);
        $this->assertTrue($entity->getSumFormal() == 0);

        $entity->setFormal(7);
        $this->assertEquals(700, (int)$entity->getSumFormal());
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
