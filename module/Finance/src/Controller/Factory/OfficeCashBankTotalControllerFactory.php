<?php

namespace Finance\Controller\Factory;

use Finance\Controller\OfficeCashBankTotalController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class OfficeCashBankTotalControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $financeTotalService = $container->get('financeTotalService');
        return new OfficeCashBankTotalController($financeTotalService);
    }
}
