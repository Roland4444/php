<?php

namespace OfficeCash\Service\Factory;

use Interop\Container\ContainerInterface;
use OfficeCash\Facade\OfficeCash;
use Zend\ServiceManager\Factory\FactoryInterface;
use OfficeCash\Entity\TransportIncome;
use OfficeCash\Service\TransportIncomeService;

class TransportIncomeServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(TransportIncome::class);
        $transport = $container->get('officeCash');
        return new TransportIncomeService($repository, $transport);
    }
}
