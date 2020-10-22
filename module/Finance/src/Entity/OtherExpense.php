<?php

namespace Finance\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

/**
 * @ORM\Entity(repositoryClass="\Finance\Repositories\OtherExpenseRepository")
 * @ORM\Table(name="main_other_expenses")
 */
class OtherExpense implements InputFilterAwareInterface
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
    private $realdate;

    /** @ORM\Column(type="string") */
    private $recipient;

    /** @ORM\Column(type="string") */
    private $inn;

    /** @ORM\Column(type="string") */
    private $comment;

    /** @ORM\Column(type="string") */
    private $money;

    /**
     * @ORM\ManyToOne(targetEntity="Finance\Entity\BankAccount")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id")
     */
    private $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="\Spare\Entity\Order")
     * @ORM\JoinTable(name="spare_order_expense_ref",
     *      joinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
     *      )
     */
    private $order;

    /** @ORM\Column(type="integer") */
    private $payment_number;

    public function __construct()
    {
        $this->order = new ArrayCollection();
    }

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

    public function setRealdate($date)
    {
        $this->realdate = $date;
    }

    public function getRealdate()
    {
        return $this->realdate;
    }

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    public function getInn()
    {
        return $this->inn;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setMoney($money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setPaymentNumber($payment_number)
    {
        $this->payment_number = (int)$payment_number;
    }

    public function getPaymentNumber()
    {
        return $this->payment_number;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order->first();
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
                'validators' => [
                    [
                        'name' => 'Date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'realdate',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'recipient',
                'required' => false,
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
                            'pattern' => '/^[а-яА-Яa-zA-Z0-9.+* \/()\",\-%№@"«»]{1,255}$/iu',
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
                            'max' => 300,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[а-яА-Яa-zA-Z0-9. ()\",\-%\'\/№;_|:\"%№@"*«»]{1,300}$/iu',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
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
}
