<?php

namespace FactoringTest\Entity;

use Factoring\Entity\Provider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Provider();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Provider::class, $this->entity);
    }

    public function testSettersAndGetters()
    {
        $entity = $this->entity;
        $entity->setId(123);
        $entity->setTitle('Test title');
        $entity->setInn('333222111');

        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('Test title', $entity->getTitle());
        $this->assertEquals('333222111', $entity->getInn());
    }
}
