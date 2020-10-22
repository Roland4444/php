<?php

namespace Factoring\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FactoringTotalFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $salesService = $container->get('factoringSalesService');
        $paymentService = $container->get('factoringPaymentService');
        $providerService = $container->get('factoringProviderService');
        $assignmentDebService = $container->get('factoringAssignmentDebtService');
        return new FactoringTotal($salesService, $paymentService, $providerService, $assignmentDebService);
    }
}
