<?php

namespace Modules\Service\Factory;

use Interop\Container\ContainerInterface;
use Modules\Service\ContainerRentalService;
use Storage\Entity\ContainerExtraOwner;

class ContainerRentalServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ContainerRentalService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mainOtherExpenseService = $container->get('financeOtherExpenseService');
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(ContainerExtraOwner::class);
        return new ContainerRentalService($mainOtherExpenseService, $repository);
    }
}
