<?php

namespace Spare\Controller\Factory;

use \Interop\Container\ContainerInterface;
use Spare\Controller\PaymentController;
use OfficeCash\Service\OtherExpenseService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PaymentControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return PaymentController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $services = [
            'sellerService' => $container->get('spareSellerService'),
            'expenseService' => $container->get('financeOtherExpenseService'),
            'storageExpenseService' => $container->get(OtherExpenseService::class),
            'orderService' => $container->get('spareOrderService'),
            'paymentService' => $container->get('sparePaymentService'),
        ];
        $accessValidateService = $container->get('accessValidateService');
        return new PaymentController($services, $accessValidateService);
    }
}
