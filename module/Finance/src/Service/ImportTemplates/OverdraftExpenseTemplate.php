<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Reference\Service\CategoryService;

class OverdraftExpenseTemplate implements Template
{
    private $order;
    private $overdraftCategory;

    public function __construct(CategoryService $categoryService)
    {
        $this->overdraftCategory = $categoryService->getByAlias('overdraft');
    }

    /**
     * {@inheritDoc}
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        return $order->getType() == 2 || $order->getType() == 16;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return 'Расход Овердрафт - ' . $this->order->getMoney();
    }

    public function getParams(): array
    {
        return ['category' => $this->overdraftCategory];
    }
}
