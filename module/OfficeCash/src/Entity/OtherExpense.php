<?php

namespace OfficeCash\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="storage_other_expense")
 */
class OtherExpense implements InputFilterAwareInterface, \JsonSerializable
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
    private $money;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /**
     * @ORM\ManyToMany(targetEntity="\Spare\Entity\Order")
     * @ORM\JoinTable(name="spare_order_cash_expense_ref",
     *      joinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
     *      )
     */
    private $order;

    /** @ORM\Column(type="string") */
    private $comment;

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

    public function setMoney($money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setDepartment($dep)
    {
        $this->department = $dep;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order->first();
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
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
                                \Zend\Validator\Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
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
                            'max' => 1000,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Zа-яА-Я0-9., ё\/\*()\-\+\"%:№"@]{1,1000}$/ui',
                            'messages' => [
                                \Zend\Validator\Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
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
        return [
            'id' => $this->getId(),
            'money' => $this->getMoney(),
            'comment' => $this->getComment(),
            'date' => $this->getDate(),
            'order' => $this->getOrder()
        ];
    }
}
