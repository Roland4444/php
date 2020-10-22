<?php

namespace ShipmentDocs\Service;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $serviceBaseUrl = $config['services']['shipment_docs']['url'];
        $guzzleClient = new Client(['base_uri' => $serviceBaseUrl]);
        return new $requestedName($guzzleClient);
    }
}
