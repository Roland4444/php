<?php

namespace Storage\Entity;

class CustomerTotal
{
    private int $customerId;
    private string $customerName;
    private float $cashPaymentSum;
    private float $cashlessPaymentSum;
    private float $formalPaymentSum;
    private float $purchaseFormalForAmountSum;
    private float $purchaseFactForAmountSum;
    private string $inspectionDate;
    private string $rowClass;
    private bool $isLegal;

    /**
     * CustomerTotal constructor.
     * @param array $customer
     */
    public function __construct(array $customer)
    {
        $this->customerId = $customer['customer_id'];
        $this->customerName = $customer['customer'];
        $this->purchaseFactForAmountSum = $customer['purchase'] ?? 0;
        $this->purchaseFormalForAmountSum = isset($customer['purchase_formal']) ? $customer['purchase_formal'] : 0;
        $this->cashPaymentSum = $customer['payment'] ?? 0;
        $this->cashlessPaymentSum = $customer['main_payment'] ?? 0;
        $this->formalPaymentSum = $customer['payment_formal'] ?? 0;
        $this->inspectionDate = $customer['inspection_date'] ?? '';
        $this->isLegal = $customer['legal'];
    }

    public function getFormalBalance(): float
    {
        return $this->purchaseFormalForAmountSum - $this->formalPaymentSum - $this->cashlessPaymentSum;
    }

    public function getFactBalance(): float
    {
        return $this->purchaseFactForAmountSum - $this->getPaymentSum();
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getPaymentSum(): float
    {
        return $this->cashPaymentSum + $this->cashlessPaymentSum;
    }

    /**
     * @param float $purchaseFactForAmountSum
     */
    public function setPurchaseFactForAmountSum(?float $purchaseFactForAmountSum): void
    {
        $this->purchaseFactForAmountSum = $purchaseFactForAmountSum ?? 0;
    }

    /**
     * @return float
     */
    public function getPurchaseFactForAmountSum(): float
    {
        return $this->purchaseFactForAmountSum;
    }

    /**
     * @return string
     */
    public function getInspectionDate(): string
    {
        return $this->inspectionDate;
    }

    /**
     * @return string
     */
    public function getRowClass(): string
    {
        return $this->rowClass ?? '';
    }

    /**
     * @param string $rowClass
     */
    public function setRowClass(string $rowClass): void
    {
        $this->rowClass = $rowClass;
    }

    /**
     * @return bool
     */
    public function isLegal(): bool
    {
        return $this->isLegal;
    }
}
