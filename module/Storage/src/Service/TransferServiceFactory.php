<?php

namespace Storage\Service;

use Interop\Container\ContainerInterface;
use Reports\Dao\RemoteSkladDao;
use Storage\Entity\Transfer;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransferServiceFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $remoteSkladDao = $container->get(RemoteSkladDao::class);
        $entityManager = $container->get('entityManager');
        $transferRepo = $entityManager->getRepository(Transfer::class);
        return new TransferService($transferRepo, $remoteSkladDao);
    }
}
