<?php

namespace Finance\Controller\Factory;

use Finance\Controller\OtherExpenseController;
use Finance\Service\BankService;
use Interop\Container\ContainerInterface;
use Reference\Service\CategoryService;
use Zend\ServiceManager\Factory\FactoryInterface;

class OtherExpenseControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $bankService = $container->get(BankService::class);
        $categoryService = $container->get(CategoryService::class);
        $otherExpenseService = $container->get('financeOtherExpenseService');
        $entityManager = $container->get('entityManager');
        $authService = $container->get('authenticationService');
        $accessService = $container->get('accessService');
        return new OtherExpenseController(
            $entityManager,
            $bankService,
            $categoryService,
            $otherExpenseService,
            $authService,
            $accessService,
        );
    }
}
