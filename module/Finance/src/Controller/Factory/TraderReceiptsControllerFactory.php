<?php

namespace Finance\Controller\Factory;

use Finance\Controller\TraderReceiptsController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Reference\Service\TraderService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TraderReceiptsControllerFactory implements FactoryInterface
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
        return new TraderReceiptsController($entityManager, $traderReceiptsService, $bankService, $traderService);
    }
}
