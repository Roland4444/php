<?php

namespace Modules\Facade;

use Interop\Container\ContainerInterface;
use Modules\Service\TransportIncasService;

class TransportFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transportIncomeService = $container->get(TransportIncasService::class);
        return new Transport($transportIncomeService);
    }
}
