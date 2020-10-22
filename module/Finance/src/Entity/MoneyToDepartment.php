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
 * @ORM\Table(name="money_to_department")
 */
class MoneyToDepartment implements InputFilterAwareInterface, \JsonSerializable
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

    /** @ORM\Column(type="boolean") */
    private $verified;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id")
     */
    private $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

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

    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    public function getVerified()
    {
        return $this->verified;
    }

    public function setBank($b)
    {
        $this->bank = $b;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setDepartment($d)
    {
        $this->department = $d;
    }

    public function getDepartment()
    {
        return $this->department;
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
                            'max' => 13,
                        ],
                    ],
                    [
                        'name' => 'Date',
                        'options' => [
                            'messages' => [
                                'dateInvalid' => 'Неверно введена дата',
                                'dateInvalidDate' => 'Неверно введена дата',
                                'dateFalseFormat' => 'Неверно введена дата'
                            ]
                        ]
                    ]
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
                            'pattern' => '/^[0-9.]{1,13}$/i',
                            'messages' => [
                                Regex::INVALID => 'Некорректно заполнено поле "Сумма"',
                                Regex::NOT_MATCH => 'Некорректно заполнено поле "Сумма"',
                                Regex::ERROROUS => 'Некорректно заполнено поле "Сумма"',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'verified',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Int',
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
            'date' => $this->getDate(),
            'bank' => $this->getBank(),
            'department' => $this->getDepartment(),
            'moneyFormat' => $currencyFormatter($this->getMoney(), 'RUR', null, 'ru_RU'),
            'money' => $this->getMoney(),
            'verified' => $this->getVerified()
        ];
    }
}
