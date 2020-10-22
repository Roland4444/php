<?php

namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\Receipt;
use Spare\Service\ReceiptService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ReceiptServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ReceiptService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Receipt::class);
        return new ReceiptService($repository);
    }
}
