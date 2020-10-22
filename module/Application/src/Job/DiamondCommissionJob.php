<?php

namespace Application\Job;

use Application\Service\DiamondCommissionService;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\Consumer;
use RabbitMqModule\ConsumerInterface;
use Zend\Log\Logger;

class DiamondCommissionJob implements ConsumerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DiamondCommissionService
     */
    private $diamondCommissionService;

    public function __construct($entityManager, $logger, $diamondCommissionService)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->diamondCommissionService = $diamondCommissionService;
    }

    /**
     * @param AMQPMessage $message
     *
     * @param Consumer $consumer
     * @return mixed
     */
    public function execute(AMQPMessage $message, Consumer $consumer)
    {
        try {
            $this->entityManager->getConnection()->connect();

            $data = json_decode($message->body, false);

            $commission = $this->diamondCommissionService->calculateCommission($data->money);
            echo 'Money: ' . $data->money. ' Commission: ' . $commission . "\n";
            if ($commission === 0) {
                return ConsumerInterface::MSG_ACK;
            }

            $date = date('Y-m-d', strtotime($data->date));

            if ($data->type == 'increase') {
                $this->diamondCommissionService->increase($date, $commission);
            } elseif ($data->type == 'decrease') {
                $this->diamondCommissionService->decrease($date, $commission);
            }
            $this->entityManager->getConnection()->close();
            return ConsumerInterface::MSG_ACK;
        } catch (\Exception $e) {
            $this->logger->err($e->getTraceAsString());
            return ConsumerInterface::MSG_REJECT_REQUEUE;
        }
    }
}
