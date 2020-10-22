<?php

namespace ApplicationTest\Job;

use Application\Job\DiamondCommissionJob;
use Application\Service\DiamondCommissionService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use RabbitMqModule\Consumer;
use RabbitMqModule\ConsumerInterface;
use Zend\Log\Logger;

class DiamondCommissionJobTest extends TestCase
{
    public function testExecuteWithException()
    {
        $entityManager = $this->getMockEntityManagerWithException();
        $logger = $this->createMock(Logger::class);
        $diamondCommissionService = $this->createMock(DiamondCommissionService::class);

        $consumer = $this->createMock(Consumer::class);

        $instance = new DiamondCommissionJob($entityManager, $logger, $diamondCommissionService);

        $result = $instance->execute(new AMQPMessage(), $consumer);

        $this->assertEquals(ConsumerInterface::MSG_REJECT_REQUEUE, $result);
    }

    /**
     * Проверка работы метода execute. Случай с нулевой комиссией
     */
    public function testExecuteWithEmptyCommission()
    {
        $entityManager = $this->getMockEntityManager();
        $logger = $this->createMock(Logger::class);
        $body = '{"money":100, "date":"' . date('Y-m-d') . '", "type":"decrease"}';
        $msg = new AMQPMessage($body);
        $consumer = $this->createMock(Consumer::class);
        $diamondCommissionService = $this->createMock(DiamondCommissionService::class);
        $diamondCommissionService->expects($this->any())->method('calculateCommission')->willReturn(0.0);

        $instance = new DiamondCommissionJob($entityManager, $logger, $diamondCommissionService);
        $result = $instance->execute($msg, $consumer);

        $this->assertEquals(ConsumerInterface::MSG_ACK, $result);
    }

    public function testExecuteIfCommissionIncreasesValue()
    {
        $commission = 100.0;

        $entityManager = $this->getMockEntityManager();
        $logger = $this->createMock(Logger::class);
        $body = '{"money":100, "date":"2015-01-02", "type":"increase"}';
        $msg = new AMQPMessage($body);
        $consumer = $this->createMock(Consumer::class);
        $diamondCommissionService = $this->createMock(DiamondCommissionService::class);
        $diamondCommissionService->expects($this->any())->method('calculateCommission')->willReturn($commission);
        $diamondCommissionService->expects($this->once())->method('increase')->with('2015-01-02', $commission);

        $instance = new DiamondCommissionJob($entityManager, $logger, $diamondCommissionService);
        $result = $instance->execute($msg, $consumer);

        $this->assertEquals(ConsumerInterface::MSG_ACK, $result);
    }

    public function testExecuteIfCommissionDecreasesValue()
    {
        $entityManager = $this->getMockEntityManager();
        $logger = $this->createMock(Logger::class);
        $body = '{"money":100, "date":"2017-05-06", "type":"decrease"}';
        $msg = new AMQPMessage($body);
        $consumer = $this->createMock(Consumer::class);
        $diamondCommissionService = $this->createMock(DiamondCommissionService::class);
        $diamondCommissionService->expects($this->any())->method('calculateCommission')->willReturn(100.0);
        $diamondCommissionService->expects($this->once())->method('decrease')->with('2017-05-06', 100);

        $instance = new DiamondCommissionJob($entityManager, $logger, $diamondCommissionService);
        $result = $instance->execute($msg, $consumer);

        $this->assertEquals(ConsumerInterface::MSG_ACK, $result);
    }

    private function getMockEntityManager()
    {
        $mockEntityManager = $this->createMock(EntityManager::class);
        $connection = $this->createMock(Connection::class);
        $mockEntityManager->expects($this->any())->method('getConnection')->willReturn($connection);
        return $mockEntityManager;
    }

    private function getMockEntityManagerWithException()
    {
        $mockEntityManager = $this->createMock(EntityManager::class);
        $mockEntityManager->expects($this->once())
            ->method('getConnection')
            ->willThrowException(new \Exception('Fake exception'));
        return $mockEntityManager;
    }
}
