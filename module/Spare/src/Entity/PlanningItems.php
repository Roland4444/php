<?php
namespace Spare\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="spare_planning_items")
 */
class PlanningItems implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Planning", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="planning_id", referencedColumnName="id")
     */
    private $planning;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

    /** @ORM\OneToMany(targetEntity="OrderItems", mappedBy="planningItem", cascade={"persist", "remove"}) */
    private $itemsOrder;

    /** @ORM\Column(type="decimal", name="quantity") */
    private $quantity;

    public function __construct()
    {
        $this->itemsOrder = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getItemsOrder()
    {
        return $this->itemsOrder;
    }

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Planning
     */
    public function getPlanning(): Planning
    {
        return $this->planning;
    }

    /**
     * @param mixed $planning
     */
    public function setPlanning(Planning $planning)
    {
        $this->planning = $planning;
    }

    /**
     * @return Spare
     */
    public function getSpare()
    {
        return $this->spare;
    }

    /**
     * @param mixed $spare
     */
    public function setSpare(Spare $spare)
    {
        $this->spare = $spare;
    }

    /**
     * Количество закупленных запчастей
     *
     * @return integer
     */
    public function getReceipted()
    {
        $quantity = 0;
        foreach ($this->itemsOrder as $itemOrder) {
            $quantity += $itemOrder->getReceipted();
        }
        return $quantity;
    }

    /**
     * Количество заказанных запчастей
     *
     * @return int
     */
    public function getOrdered()
    {
        $quantity = 0;
        $isComposite = $this->spare->getIsComposite();
        foreach ($this->itemsOrder as $itemOrder) {
            if ($isComposite) {
                $quantity += $itemOrder->getQuantity() * $itemOrder->getSubQuantity();
            } else {
                $quantity += $itemOrder->getQuantity();
            }
        }
        return $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function isDeletable()
    {
        return count($this->itemsOrder) == 0;
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
                'name'     => 'orderQuantity',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'spare',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
