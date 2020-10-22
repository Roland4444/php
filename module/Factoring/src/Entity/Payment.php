<?php

namespace Factoring\Entity;

use Core\Entity\AbstractEntity;
use JsonSerializable;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Factoring\Repository\PaymentRepository")
 * @ORM\Table(name="factoring_payments")
 */
class Payment implements AbstractEntity, InputFilterAwareInterface, JsonSerializable
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Factoring\Entity\Provider", fetch="EAGER")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private $provider;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Trader", fetch="EAGER")
     * @ORM\JoinColumn(name="trader_id", referencedColumnName="id")
     */
    private $trader;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount", fetch="EAGER")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id")
     */
    private $bank;

    /** @ORM\Column(type="string") */
    private $money;

    /** @ORM\Column(type="integer") */
    private $payment_number;

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
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return mixed
     */
    public function getTrader()
    {
        return $this->trader;
    }

    /**
     * @param mixed $trader
     */
    public function setTrader($trader): void
    {
        $this->trader = $trader;
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
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'date',
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'money',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 15,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,15}$/i',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'provider' => $this->getProvider()->getTitle(),
            'trader' => $this->getTrader() ? $this->getTrader()->getName() : '',
            'bank' => $this->getBank()->getName(),
            'money' => $this->getMoney(),
        ];
    }
}
