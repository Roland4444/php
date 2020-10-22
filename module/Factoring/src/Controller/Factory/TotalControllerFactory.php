<?php

namespace Factoring\Controller\Factory;

use Factoring\Controller\TotalController;
use \Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TotalControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TotalController
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $salesService = $container->get('factoringSalesService');
        $paymentService = $container->get('factoringPaymentService');
        $providerService = $container->get('factoringProviderService');
        $assignmentDebService = $container->get('factoringAssignmentDebtService');
        return new TotalController($salesService, $paymentService, $providerService, $assignmentDebService);
    }
}
