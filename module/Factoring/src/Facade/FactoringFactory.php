<?php

namespace Factoring\Facade;

use Interop\Container\ContainerInterface;

class FactoringFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paymentService = $container->get('factoringPaymentService');
        $assignmentDebtService = $container->get('factoringAssignmentDebtService');
        $providerService = $container->get('factoringProviderService');
        return new Factoring($paymentService, $assignmentDebtService, $providerService);
    }
}
