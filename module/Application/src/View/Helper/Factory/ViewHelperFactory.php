<?php
namespace Application\View\Helper\Factory;

use Application\View\Helper\ViewHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

final class ViewHelperFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ViewHelper($container);
    }
}
