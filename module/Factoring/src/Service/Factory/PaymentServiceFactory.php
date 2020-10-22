<?php
namespace Factoring\Service\Factory;

use Factoring\Entity\Payment;
use Factoring\Service\PaymentService;
use Interop\Container\ContainerInterface;

class PaymentServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PaymentService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('entityManager');
        $repository = $entityManager->getRepository(Payment::class);
        return new PaymentService($repository);
    }
}
