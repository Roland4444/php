<?php

namespace Finance\Controller\Factory;

use Finance\Controller\OfficeCashTransferController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class OfficeCashTransferControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $cashTransferService = $container->get('cashTransferService');
        $bankService = $container->get(BankService::class);
        return new OfficeCashTransferController($entityManager, $cashTransferService, $bankService);
    }
}
