<?php

namespace Finance\Controller\Factory;

use Finance\Controller\OfficeOtherReceiptsController;
use Finance\Service\BankService;
use Finance\Service\OtherReceiptService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class OfficeOtherReceiptsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $receiptService = $container->get(OtherReceiptService::class);
        $bankService = $container->get(BankService::class);
        return new OfficeOtherReceiptsController($receiptService, $bankService);
    }
}
