<?php

namespace Factoring\Controller\Factory;

use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Reference\Service\DepartmentService;
use Zend\ServiceManager\Factory\FactoryInterface;

class PaymentControllerFactory implements FactoryInterface
{
    protected $service = 'factoringPaymentService';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get($this->service);
        $services = [
            DepartmentService::class => $container->get(DepartmentService::class),
            'dateService' => $container->get('dateService'),
            'bankService' => $container->get(BankService::class),
            'form' => $container->get('factoringPaymentForm')
        ];
        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
