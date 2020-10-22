<?php
namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccessServiceFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return AccessService
     * @throws
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $acl = $container->get('acl');
        $authService = $container->get('authenticationService');
        return new AccessService($acl, $authService);
    }
}
