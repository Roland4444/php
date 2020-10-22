<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Category;
use Reference\Entity\Department;
use OfficeCash\Entity\OtherExpense;
use Zend\InputFilter\InputFilter;

class OtherExpenseTest extends TestCase
{
    /**
     * @var OtherExpense
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new OtherExpense();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(OtherExpense::class, $this->entity);
    }

    public function testGettersDefaultValues()
    {
        $entity = $this->entity;

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertNull($entity->getRealdate());
        $this->assertNull($entity->getMoney());
        $this->assertNull($entity->getCategory());
        $this->assertNull($entity->getDepartment());
        $this->assertNull($entity->getComment());
    }

    public function testPlainSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate('2000-01-01');
        $entity->setRealdate('2111-01-01');
        $entity->setMoney(500);
        $entity->setCategory(new Category());
        $entity->setDepartment(new Department());
        $entity->setComment('test comment');

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('2000-01-01', $entity->getDate());
        $this->assertEquals('2111-01-01', $entity->getRealdate());
        $this->assertEquals(500, $entity->getMoney());
        $this->assertInstanceOf(Category::class, $entity->getCategory());
        $this->assertInstanceOf(Department::class, $entity->getDepartment());
        $this->assertEquals('test comment', $entity->getComment());
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
