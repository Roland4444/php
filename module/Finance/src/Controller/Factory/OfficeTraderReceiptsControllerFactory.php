<?php

namespace Finance\Controller\Factory;

use Finance\Controller\OfficeTraderReceiptsController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Reference\Service\TraderService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OfficeTraderReceiptsControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $traderReceiptsService = $container->get('traderReceiptsService');
        $bankService = $container->get(BankService::class);
        $traderService = $container->get(TraderService::class);
        return new OfficeTraderReceiptsController($entityManager, $traderReceiptsService, $bankService, $traderService);
    }
}
