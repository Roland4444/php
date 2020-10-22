<?php

namespace Finance\Controller\Factory;

use Finance\Controller\TemplateController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TemplateControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $templateService = $container->get('financePaymentTemplateService');
        $bankClientService = $container->get('bankClientService');
        $entityManager = $container->get('entityManager');
        $authService = $container->get('authenticationService');
        return new TemplateController($templateService, $bankClientService, $entityManager, $authService);
    }
}
