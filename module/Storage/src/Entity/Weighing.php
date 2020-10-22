<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;
use Reference\Entity\Customer;
use Reference\Entity\Department;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\WeighingRepository")
 * @ORM\Table(name="weighings")
 */
class Weighing implements InputFilterAwareInterface, JsonSerializable
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $waybill;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $time;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(name="export_id", type="integer")
     */
    private $exportId;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity="WeighingItem", mappedBy="weighing",cascade={"persist","remove"}))
     */
    private $weighingItems;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="Storage\Entity\MetalExpense", mappedBy="weighing")
     */
    private $metalExpenses;

    public function __construct()
    {
        $this->weighingItems = new ArrayCollection();
        $this->metalExpenses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getWaybill()
    {
        return $this->waybill;
    }

    public function setWaybill($waybill): void
    {
        $this->waybill = $waybill;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time): void
    {
        $this->time = $time;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    public function getExportId()
    {
        return $this->exportId;
    }

    public function setExportId($exportId): void
    {
        $this->exportId = $exportId;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment($department): void
    {
        $this->department = $department;
    }

    public function getWeighingItems()
    {
        return $this->weighingItems;
    }

    public function setWeighingItems($weighingItems): void
    {
        $this->weighingItems = $weighingItems;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    public function getMetalExpenses()
    {
        return $this->metalExpenses;
    }

    public function setMetalExpenses($metalExpenses): void
    {
        $this->metalExpenses = $metalExpenses;
    }

    /**
     * Добавить weighing item в коллекцию
     * @param WeighingItem $weighingItem
     */
    public function addItem(WeighingItem $weighingItem)
    {
        $this->weighingItems->add($weighingItem);
    }

    /**
     * Масса всех взвешиваний
     */
    public function getTotalMass()
    {
        $total = 0;
        foreach ($this->getWeighingItems() as $weighingItem) {
            $total += $weighingItem->getMass();
        }
        return round($total, 2);
    }

    /**
     * Масса всех взвешиваний
     */
    public function getTotalPrice()
    {
        $total = 0;
        foreach ($this->getWeighingItems() as $weighingItem) {
            $total += $weighingItem->getPrice() * $weighingItem->getMass();
        }
        return round($total, 2);
    }

    /**
     * Вся оплаченная сумма взвпешиваний
     */
    public function getTotalPaidAmount()
    {
        $paidAmount = 0;

        if (! empty($this->getMetalExpenses())) {
            foreach ($this->getMetalExpenses() as $expense) {
                $paidAmount += $expense->getMoney();
            }
        }

        return $paidAmount;
    }

    public function hasBeenPaid()
    {
        return count($this->getMetalExpenses()) !== 0;
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

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'id',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Digits',
                    ],
                ]
            ]);

            $inputFilter->add([
                'name'     => 'waybill',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Digits',
                    ],
                ]
            ]);

            $inputFilter->add([
                'name'     => 'date',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Date',
                    ],
                ]
            ]);

            $inputFilter->add([
                'name'     => 'time',
                'required' => true
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'waybill' => $this->waybill,
            'date' => $this->date,
            'time' => $this->time,
            'comment' => $this->comment,
            'exportId' => $this->exportId,
            'department' => $this->getDepartment(),
            'departmentId' => $this->getDepartment()->getId(),
            'totalMass' => $this->getTotalMass(),
            'totalPrice' => $this->getTotalPrice(),
            'weighings' => $this->getWeighingItems()->toArray(),
            'customer' => $this->getCustomer(),
            'totalPaidAmount' => $this->getTotalPaidAmount(),
            'hasBeenPaid' => $this->hasBeenPaid()
        ];
    }
}
