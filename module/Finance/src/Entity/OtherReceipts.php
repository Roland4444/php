<?php

namespace Finance\Entity;

use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;
use Core\Entity\EntityWithOptions;

/**
 * @ORM\Entity
 * @ORM\Table(name="main_receipts")
 */
class OtherReceipts implements InputFilterAwareInterface, \JsonSerializable
{
    use EntityWithOptions;

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

    /** @ORM\Column(type="string") */
    private $comment;

    /** @ORM\Column(type="bigint") */
    private $payment_number;

    /** @ORM\Column(type="string") */
    private $inn;

    /** @ORM\Column(type="string") */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount")
     * @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     */
    private $bank;

    /** @ORM\Column(type="json") */
    private $options;

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

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
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

    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    public function getInn()
    {
        return $this->inn;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function isOverdraft()
    {
        return $this->hasOption(Options::OVERDRAFT);
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

            $inputFilter->add([
                'name' => 'comment',
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
                            'max' => 200,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[а-яА-Яa-zA-Z0-9. ()\",\-%\'\/№;_|:"@*]{1,300}$/iu',
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
        $currencyFormatter = new CurrencyFormat();
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'sender' => $this->getSender(),
            'bank' => $this->getBank(),
            'comment' => $this->getComment(),
            'inn' => $this->getInn(),
            'moneyFormat' => $currencyFormatter($this->getMoney(), 'RUR'),
            'money' => $this->getMoney(),
            'overdraft' => $this->isOverdraft(),
        ];
    }
}
