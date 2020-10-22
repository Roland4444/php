<?php


namespace Reference\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Reference\Entity\Vehicle;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class VehicleServiceFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Vehicle::class);
        return new VehicleService($repository);
    }
}
