<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Service\TotalService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TotalServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TotalService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $consumptionService = $container->get('spareConsumptionService');
        $receiptService = $container->get('spareReceiptService');
        $transferService = $container->get('spareTransferService');
        $inventoryService = $container->get('spareInventoryService');
        $spareService = $container->get('spareService');
        return new TotalService([
            'consumptionService' => $consumptionService,
            'receiptService' => $receiptService,
            'transferService' => $transferService,
            'inventoryService' => $inventoryService,
            'spareService' => $spareService,
        ]);
    }
}
