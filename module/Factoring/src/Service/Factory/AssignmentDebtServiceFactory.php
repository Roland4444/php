<?php
namespace Factoring\Service\Factory;

use Factoring\Entity\AssignmentDebt;
use Factoring\Service\AssignmentDebtService;
use Interop\Container\ContainerInterface;

class AssignmentDebtServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return AssignmentDebtService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(AssignmentDebt::class);
        return new AssignmentDebtService($repository);
    }
}
