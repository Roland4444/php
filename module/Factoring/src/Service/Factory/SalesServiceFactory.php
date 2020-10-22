<?php
namespace Factoring\Service\Factory;

use Factoring\Service\SalesService;
use Interop\Container\ContainerInterface;

class SalesServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return SalesService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $storage = $container->get('storage');
        return new SalesService($storage);
    }
}
