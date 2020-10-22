<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Department;
use Storage\Entity\CashTransfer;
use Zend\InputFilter\InputFilter;

class CashTransferTest extends TestCase
{
    /**
     * @var CashTransfer
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new CashTransfer();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(CashTransfer::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate(date('Y-m-d'));
        $entity->setSource(new Department());
        $entity->setDest(new Department());
        $entity->setMoney(500);

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertInstanceOf(Department::class, $entity->getSource());
        $this->assertInstanceOf(Department::class, $entity->getDest());
        $this->assertEquals(500, $entity->getMoney());
    }

    public function testNullDate()
    {
        $this->assertEquals(date('Y-m-d'), $this->entity->getDate());
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
