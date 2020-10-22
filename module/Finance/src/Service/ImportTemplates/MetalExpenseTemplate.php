<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Reference\Service\CustomerService;

class MetalExpenseTemplate implements Template
{
    private CustomerService $customerService;
    private array $params = [];
    private Order $order;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @inheritDoc
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        $customer = $this->customerService->findByInn($order->getDestInn());
        if ($customer !== null) {
            $this->params['customer'] = $customer;
        }
        return $customer !== null && $order->getType() === '01' && mb_strpos($order->getComment(), 'за лом') !== false;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return 'Оплата за лом ' . $this->order->getMoney();
    }

    /**
     * @inheritDoc
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
