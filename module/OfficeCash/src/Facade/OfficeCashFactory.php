<?php

namespace OfficeCash\Facade;

use Interop\Container\ContainerInterface;
use OfficeCash\Service\OtherExpenseService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Modules\Facade\Transport;

class OfficeCashFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transport = $container->get(Transport::class);
        $expanseService = $container->get(OtherExpenseService::class);
        return new OfficeCash($transport, $expanseService);
    }
}
