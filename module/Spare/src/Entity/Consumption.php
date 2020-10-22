<?php
namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Reference\Entity\Employee;
use Reference\Entity\Warehouse;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\ConsumptionRepository")
 * @ORM\Table(name="spare_consumption")
 */
class Consumption implements InputFilterAwareInterface, AbstractEntity, JsonSerializable
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     */
    private $employee;

    /** @ORM\Column(type="string") */
    private $date;

    /** @ORM\OneToMany(targetEntity="ConsumptionItem", mappedBy="consumption", cascade={"persist", "remove"},
     *      orphanRemoval=true, fetch="EAGER") */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items): void
    {
        $this->items = $items;
    }

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse($warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function addItem(ConsumptionItem $consumptionItem)
    {
        $this->items->add($consumptionItem);
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
                'name'     => 'date',
                'required' => true,
            ]);

            $inputFilter->add([
                'name' => 'employee',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'employee' => $this->employee,
            'id' => $this->id,
            'date' => $this->date,
            'data' => $this->getItems()->toArray()
        ];
    }
}
