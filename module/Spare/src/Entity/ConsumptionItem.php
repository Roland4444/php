<?php


namespace Spare\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Entity\AbstractEntity;
use JsonSerializable;
use Reference\Entity\Vehicle;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\ConsumptionItemRepository")
 * @ORM\Table(name="spare_consumption_items")
 */
class ConsumptionItem implements AbstractEntity, JsonSerializable, InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="decimal") */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare", fetch="EAGER")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

    /** @ORM\Column(type="string") */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="Consumption", inversedBy="items", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="consumption_id", referencedColumnName="id")
     */
    private $consumption;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getSpare(): Spare
    {
        return $this->spare;
    }

    public function setSpare($spare): void
    {
        $this->spare = $spare;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle($vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function getConsumption()
    {
        return $this->consumption;
    }

    public function setConsumption($consumption): void
    {
        $this->consumption = $consumption;
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
                'name'     => 'quantity',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'spare' => $this->spare,
            'editableSpare' => $this->spare,
            'vehicle' => $this->vehicle,
            'quantity' => $this->quantity,
            'comment' => $this->comment
        ];
    }
}
