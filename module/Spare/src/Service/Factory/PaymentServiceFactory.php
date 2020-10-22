<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Service\PaymentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PaymentServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PaymentService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $expenseService = $container->get('financeOtherExpenseService');
        $orderService = $container->get('spareOrderService');
        $sellerService = $container->get('spareSellerService');
        $spare = $container->get('spare');
        return new PaymentService($expenseService, $orderService, $spare, $sellerService);
    }
}
