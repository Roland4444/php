<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Metal;
use Storage\Entity\Container;
use Storage\Entity\ContainerItem;
use Storage\Entity\Shipment;
use Zend\InputFilter\InputFilter;

class ContainerItemTest extends TestCase
{
    /**
     * @var ContainerItem
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new ContainerItem();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(ContainerItem::class, $this->entity);
    }

    public function testPlainSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setContainer(new Container());
        $entity->setMetal(new Metal());
        $entity->setWeight(500);
        $entity->setRealWeight(450);
        $entity->setCostDol(5);
        $entity->setComment('test comment');

        $this->assertEquals(123, $entity->getId());
        $this->assertInstanceOf(Container::class, $entity->getContainer());
        $this->assertInstanceOf(Metal::class, $entity->getMetal());
        $this->assertEquals(500, $entity->getWeight());
        $this->assertEquals(450, $entity->getRealWeight());
        $this->assertEquals(5, $entity->getCostDol());
        $this->assertEquals('test comment', $entity->getComment());
    }

    public function testGetCostWhenCostDolNull()
    {
        $entity = $this->entity;
        $entity->setCost(100);
        $this->assertEquals(100, $entity->getCost());
    }

    public function testGetCostWhenCostDolNotNull()
    {
        $shipment = new Shipment();
        $shipment->setRate(65);
        $container = new Container();
        $container->setShipment($shipment);

        $entity = $this->entity;
        $entity->setContainer($container);
        $entity->setCost(100);
        $entity->setCostDol(25);
        $this->assertEquals(1625, $entity->getCost());
    }

    public function testGetSumWhenCostDolNull()
    {
        $entity = $this->entity;
        $entity->setCost(100);
        $entity->setRealWeight(30);
        $this->assertEquals(3, $entity->getSum());
    }

    public function testGetSumWhenCostDolNotNull()
    {
        $shipment = new Shipment();
        $shipment->setRate(65);
        $container = new Container();
        $container->setShipment($shipment);

        $entity = $this->entity;
        $entity->setContainer($container);
        $entity->setCost(100);
        $entity->setCostDol(25);
        $entity->setRealWeight(30);
        $this->assertEquals(48.75, $entity->getSum());
    }

    public function testGetSumWhenCostAndCostDolNull()
    {
        $entity = $this->entity;
        $this->assertEquals(0, $entity->getSum());
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
