<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\ContainerOwner;
use Storage\Entity\Container;
use Storage\Entity\ContainerExtraOwner;
use Zend\InputFilter\InputFilter;

class ContainerExtraOwnerTest extends TestCase
{
    /**
     * @var ContainerExtraOwner
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new ContainerExtraOwner();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(ContainerExtraOwner::class, $this->entity);
    }

    public function testPlainSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setOwner(new ContainerOwner());
        $entity->setContainer(new Container());
        $entity->setOwnerCost(500);
        $entity->setIsPaid(true);

        $this->assertEquals(123, $entity->getId());
        $this->assertInstanceOf(ContainerOwner::class, $entity->getOwner());
        $this->assertInstanceOf(Container::class, $entity->getContainer());
        $this->assertEquals(500, $entity->getOwnerCost());
        $this->assertEquals(true, $entity->getIsPaid());
    }

    public function testSetAndGetDateFormal()
    {
        $entity = $this->entity;
        $entity->setDateFormal(date('Y-m-d'));

        $this->assertInstanceOf(\DateTime::class, $entity->getDateFormal());
        $this->assertEquals(date('Y-m-d'), $entity->getDateFormal()->format('Y-m-d'));

        $entity->setDateFormal(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $entity->getDateFormal());
        $this->assertEquals(date('Y-m-d'), $entity->getDateFormal()->format('Y-m-d'));
    }

    public function testGetDateFormalStringWhenDateFormalNull()
    {
        $entity = $this->entity;
        $this->assertNull($entity->getDateFormalString());
    }

    public function testGetDateFormalStringWhenDateFormalNotNull()
    {
        $entity = $this->entity;
        $entity->setDateFormal(date('Y-m-d'));
        $this->assertEquals(date('Y-m-d'), $entity->getDateFormalString());
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
