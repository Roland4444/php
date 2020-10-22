<?php

namespace Factoring\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AssignmentDebtControllerFactory implements FactoryInterface
{
    protected $service = 'factoringAssignmentDebtService';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $logger = $container->get('MyLogger');
        $service = $container->get($this->service);
        $services = [
            'form' => $container->get('factoringAssignmentDebtForm')
        ];
        return new $requestedName($entityManager, $logger, $service, $services);
    }
}
