<?php

namespace Factoring\Facade;

use Factoring\Entity\Payment;
use Factoring\Service\AssignmentDebtService;
use Factoring\Service\PaymentService;
use Factoring\Service\ProviderService;
use Finance\Entity\Order;

class Factoring
{
    private $paymentService;
    private $assignmentDebtService;
    private $providerService;

    public function __construct(
        PaymentService $paymentService,
        AssignmentDebtService $assignmentDebtService,
        ProviderService $providerService
    ) {
        $this->paymentService = $paymentService;
        $this->assignmentDebtService = $assignmentDebtService;
        $this->providerService = $providerService;
    }

    public function getSumByPeriodGroupByBank($dateEnd): array
    {
        return $this->paymentService->getSumByPeriodGroupByBank($dateEnd);
    }

    public function getAssignmentDebtGroupByTrader($dateTo)
    {
        return $this->assignmentDebtService->getSumGroupByTrader($dateTo);
    }

    public function getPaymentGroupByTrader($dateTo)
    {
        return $this->paymentService->getSumGroupByTrader($dateTo);
    }

    public function saveFromOrder(Order $order, $params = [])
    {
        $payment = new Payment();
        $payment->setDate($order->getDate());
        $payment->setBank($order->getDest());
        $payment->setMoney($order->getMoney());
        $payment->setPaymentNumber($order->getNumber());
        $payment->setProvider($this->providerService->getReference(1));
        if (! empty($params['trader'])) {
            $payment->setTrader($params['trader']);
        }
        $this->paymentService->save($payment);
    }
}
