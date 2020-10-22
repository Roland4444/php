<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Department;
use Reference\Entity\Metal;
use Storage\Entity\Transfer;
use Zend\InputFilter\InputFilter;

class TransferTest extends TestCase
{
    /**
     * @var Transfer
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Transfer();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Transfer::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate(new \DateTime());
        $entity->setSource(new Department());
        $entity->setDest(new Department());
        $entity->setMetal(new Metal());
        $entity->setWeight(1000);
        $entity->setActual(999);
        $entity->setNakl1(1);
        $entity->setNakl2(2);

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals((new \DateTime())->format('Y-m-d'), $entity->getDate()->format('Y-m-d'));
        $this->assertInstanceOf(Department::class, $entity->getSource());
        $this->assertInstanceOf(Department::class, $entity->getDest());
        $this->assertInstanceOf(Metal::class, $entity->getMetal());
        $this->assertEquals(1000, $entity->getWeight());
        $this->assertEquals(999, $entity->getActual());
        $this->assertEquals(1, $entity->getNakl1());
        $this->assertEquals(2, $entity->getNakl2());
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
