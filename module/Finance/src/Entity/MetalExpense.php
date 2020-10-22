<?php

namespace Finance\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\I18n\View\Helper\CurrencyFormat;

/**
 * @ORM\Entity(repositoryClass="\Finance\Repositories\MetalExpenseRepository")
 * @ORM\Table(name="main_metal_expenses")
 */
class MetalExpense implements AbstractEntity, \JsonSerializable
{
    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id")
     */
    private $bank;

    /** @ORM\Column(type="string") */
    private $date;

    /** @ORM\Column(type="bigint") */
    private $payment_number;

    /** @ORM\Column(type="string") */
    private $money;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank): void
    {
        $this->bank = $bank;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getPaymentNumber()
    {
        return $this->payment_number;
    }

    /**
     * @param mixed $payment_number
     */
    public function setPaymentNumber($payment_number): void
    {
        $this->payment_number = $payment_number;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param mixed $money
     */
    public function setMoney($money): void
    {
        $this->money = $money;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $currencyFormatter = new CurrencyFormat();
        return [
            'id' => $this->getId(),
            'customer' => $this->getCustomer(),
            'bank' => $this->getBank(),
            'date' => $this->getDate(),
            'money' => $this->getMoney(),
            'moneyFormat' => $currencyFormatter($this->getMoney(), 'RUR', null, 'ru_RU'),
        ];
    }
}
