<?php

namespace Finance\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

/**
 * @ORM\Entity
 * @ORM\Table(name="main_receipts_trader")
 */
class TraderReceipts implements InputFilterAwareInterface, \JsonSerializable
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $date;

    /** @ORM\Column(type="string") */
    private $money;

    /** @ORM\Column(type="bigint") */
    private $payment_number;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount")
     * @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     */
    private $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Trader")
     * @ORM\JoinColumn(name="trader_id", referencedColumnName="id")
     */
    private $trader;

    /** @ORM\Column(name="`type`",type="string") */
    private $type;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        if (! $this->date) {
            return date('Y-m-d');
        }
        return $this->date;
    }

    public function setMoney($money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
    }

    public function setPaymentNumber($pn)
    {
        $this->payment_number = (int)$pn;
    }

    public function getPaymentNumber()
    {
        return $this->payment_number;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setTrader($trader)
    {
        $this->trader = $trader;
    }

    public function getTrader()
    {
        return $this->trader;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

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
                            'max' => 10,
                        ],
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
                            'max' => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,10}$/i',
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

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $currencyFormatter = new CurrencyFormat();
        return [
            'id' => $this->getId(),
            'money' => $this->getMoney(),
            'moneyFormat' => $currencyFormatter($this->getMoney(), 'RUR', null, 'ru_RU'),
            'date' => $this->getDate(),
            'bank' => $this->getBank(),
            'trader' => $this->getTrader(),
            'type' => $this->getType(),
        ];
    }
}
