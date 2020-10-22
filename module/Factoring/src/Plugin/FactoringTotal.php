<?php

namespace Factoring\Plugin;

use Factoring\Service\AssignmentDebtService;
use Factoring\Service\PaymentService;
use Factoring\Service\ProviderService;
use Factoring\Service\SalesService;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FactoringTotal extends AbstractPlugin
{
    private $salesService;
    private $paymentService;
    private $providerService;
    private $assignmentDebtService;

    public function __construct(
        SalesService $salesService,
        PaymentService $paymentService,
        ProviderService $providerService,
        AssignmentDebtService $assignmentDebtService
    ) {
        $this->salesService = $salesService;
        $this->paymentService = $paymentService;
        $this->providerService = $providerService;
        $this->assignmentDebtService = $assignmentDebtService;
    }

    public function __invoke($dateEnd)
    {
        $provider = $this->providerService->find(1);
        $salesSum = $this->salesService->getSumFactoring($dateEnd);
        $assignmentDebtSum = $this->assignmentDebtService->getSumFactoring($dateEnd);
        $paymentSum = $this->paymentService->getSumFactoring($dateEnd);
        $sum = $salesSum + $assignmentDebtSum - $paymentSum;
        $currencyFormatter = new CurrencyFormat();
        return [
            'title' => $provider->getTitle(),
            'total' => $currencyFormatter($sum, 'RUR')
        ];
    }
}
