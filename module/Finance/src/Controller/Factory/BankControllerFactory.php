<?php

namespace Finance\Controller\Factory;

use Finance\Controller\BankController;
use Finance\Form\BankForm;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get(BankService::class);
        $form = $container->get(BankForm::class);

        return new BankController($service, $form);
    }
}
