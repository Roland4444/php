<?php

namespace Reports\Controller\Factory;

use Interop\Container\ContainerInterface;
use Reports\Controller\ExpensesController;
use Zend\ServiceManager\Factory\FactoryInterface;

class ExpensesControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get('reportExpensesService');
        return new ExpensesController($service);
    }
}
