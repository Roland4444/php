<?php
namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\PlanningRepository")
 * @ORM\Table(name="spare_planning")
 */
class Planning implements InputFilterAwareInterface, AbstractEntity
{
    const STATUS_NEW = 'Новая';
    const STATUS_IN_WORK = 'В работе';
    const STATUS_ORDERED = 'Заказано';
    const STATUS_CLOSED = 'Закрыто';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_WORK,
        self::STATUS_ORDERED,
        self::STATUS_CLOSED,
    ];

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string",name="date") */
    private $date;

    /** @ORM\Column(type="string") */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\PlanningStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /** @ORM\OneToMany(targetEntity="PlanningItems", mappedBy="planning", cascade={"persist", "remove"}, fetch="EAGER") */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return PlanningStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(PlanningStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return mixed
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    public function hasItems()
    {
        return count($this->items) != 0;
    }

    public function setItems(array $items)
    {
        if ($items) {
            foreach ($items as $item) {
                $this->items->add($item);
                $item->setPlanning($this);
            }
        }
    }

    public function haveAllItemsBeenOrdered(): bool
    {
        foreach ($this->items as $planningItem) {
            if ($planningItem->getOrdered() != $planningItem->getQuantity()) {
                return false;
            }
        }
        return true;
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

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
