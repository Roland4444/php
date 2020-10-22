<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\MetalExpenseRepository")
 * @ORM\Table(name="storage_metal_expense")
 */
class MetalExpense implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $date;

    /** @ORM\Column(type="decimal") */
    private $money;

    /** @ORM\Column(type="boolean") */
    private $formal;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity="Storage\Entity\PurchaseDeal", inversedBy="payments")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id")
     */
    private $deal;

    /** @ORM\Column(type="boolean") */
    private $diamond;

    /**
     * @ORM\ManyToOne(targetEntity="Storage\Entity\Weighing")
     * @ORM\JoinColumn(name="weighing_id", referencedColumnName="id", nullable=true)
     */
    private $weighing;

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

    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setDepartment($dep)
    {
        $this->department = $dep;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function setFormal($formal)
    {
        $this->formal = $formal;
    }

    public function getFormal()
    {
        return $this->formal;
    }

    /**
     * @return mixed
     */
    public function getDeal()
    {
        return $this->deal;
    }

    /**
     * @param mixed $deal
     */
    public function setDeal($deal): void
    {
        $this->deal = $deal;
    }

    /**
     * @return mixed
     */
    public function getDiamond()
    {
        return $this->diamond;
    }

    /**
     * @param mixed $diamond
     */
    public function setDiamond($diamond): void
    {
        $this->diamond = $diamond;
    }

    public function getWeighing()
    {
        return $this->weighing;
    }

    public function setWeighing($weighing): void
    {
        $this->weighing = $weighing;
    }


    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
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
                'name' => 'formal',
                'required' => false,
            ]);
            $inputFilter->add([
                'name' => 'diamond',
                'required' => false,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
