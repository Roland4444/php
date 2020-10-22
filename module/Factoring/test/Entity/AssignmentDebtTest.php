<?php

namespace FactoringTest\Entity;

use Exception;
use Factoring\Entity\AssignmentDebt;
use Factoring\Entity\Provider;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Trader;
use Zend\InputFilter\InputFilter;

class AssignmentDebtTest extends TestCase
{
    /**
     * @var AssignmentDebt
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new AssignmentDebt();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(AssignmentDebt::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setDate(date('Y-m-d'));
        $entity->setMoney(500);
        $entity->setProvider(new Provider());
        $entity->setTrader(new Trader());

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getDate());
        $this->assertEquals(500, $entity->getMoney());
        $this->assertInstanceOf(Provider::class, $entity->getProvider());
        $this->assertInstanceOf(Trader::class, $entity->getTrader());
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
