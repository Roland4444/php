<?php

namespace Finance\Controller\Factory;

use Finance\Controller\CashTransferController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CashTransferControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $cashTransferService = $container->get('cashTransferService');
        $bankService = $container->get(BankService::class);
        return new CashTransferController($entityManager, $cashTransferService, $bankService);
    }
}
