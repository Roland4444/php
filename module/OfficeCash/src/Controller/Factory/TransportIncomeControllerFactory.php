<?php

namespace OfficeCash\Controller\Factory;

use Interop\Container\ContainerInterface;
use OfficeCash\Controller\TransportIncomeController;
use OfficeCash\Service\TransportIncomeService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransportIncomeControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(TransportIncomeService::class);
        return new TransportIncomeController($service);
    }
}
