<?php

namespace StorageTest\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Reference\Entity\ContainerOwner;
use Storage\Entity\Container;
use Storage\Entity\ContainerExtraOwner;
use Storage\Entity\ContainerItem;
use Storage\Entity\Shipment;
use Zend\InputFilter\InputFilter;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Container();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Container::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $testItem1 = new ContainerItem();
        $testItem1->setWeight(100);
        $testItem1->setRealWeight(99);
        $testItem1->setCost(55);

        $testItem2 = new ContainerItem();
        $testItem2->setWeight(20);
        $testItem2->setRealWeight(19);
        $testItem2->setCost(120);

        $entity = $this->entity;
        $entity->setId(123);
        $entity->setName('Test name');
        $entity->setShipment(new Shipment());
        $entity->addItems([$testItem1, $testItem2]);
        $entity->setTariffCost(500);

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('Test name', $entity->getName());
        $this->assertInstanceOf(Shipment::class, $entity->getShipment());
        $this->assertInstanceOf(ContainerItem::class, $entity->getItems()[0]);
        $this->assertEquals(500, $entity->getTariffCost());
        $this->assertNull($entity->getExtraOwner());
        $this->assertEquals(500, $entity->getTariffCost());
        $this->assertEquals(120, $entity->getWeight());
        $this->assertEquals(118, $entity->getRealWeight());
        $this->assertEquals(2, $entity->getCountItems());
        $this->assertEquals(7.725, $entity->getSum());
    }

    public function testGetArrayCopy()
    {
        $extraOwner = new ContainerExtraOwner();
        $extraOwner->setOwner(new ContainerOwner());

        $entity = $this->entity;
        $entity->setName('Test');

        $entity->setExtraOwner($extraOwner);
        $arrayCopy = $entity->getArrayCopy();

        $this->assertEquals('Test', $arrayCopy['name']);
        $this->assertInstanceOf(ContainerOwner::class, $arrayCopy['owner']);
    }

    public function testExchangeArray()
    {
        $entity = $this->entity;
        $inputData = ['name' => 'qwe', 'testfield' => 123];

        $entity->exchangeArray($inputData);

        $this->assertEquals('qwe', $entity->getName());
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
