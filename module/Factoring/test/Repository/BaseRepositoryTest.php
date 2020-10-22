<?php

namespace FactoringTest\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use Factoring\Entity\AssignmentDebt;
use Factoring\Repository\AssignmentDebtRepository;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;

abstract class BaseRepositoryTest extends TestCase
{
    private $entityManager;

    public function setUp(): void
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');

        $mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['flush','persist', 'createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityManager->method('flush')->willReturn(true);
        $mockEntityManager->method('persist')->willReturn(true);
        $mockEntityManager->method('createQueryBuilder')->willReturn(new MockQueryBuilder($entityManager));

        $this->entityManager = $mockEntityManager;
    }

    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    abstract protected function getRepository();
}
