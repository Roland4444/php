<?php
namespace Spare\Service\Factory;

use Interop\Container\ContainerInterface;
use Spare\Entity\Transfer;
use Spare\Service\TransferService;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransferServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TransferService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Transfer::class);
        return new TransferService($repository);
    }
}
