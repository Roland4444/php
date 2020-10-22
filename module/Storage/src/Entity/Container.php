<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use JsonSerializable;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\ContainerRepository")
 * @ORM\Table(name="containers")
 */
class Container implements InputFilterAwareInterface, JsonSerializable
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Shipment", inversedBy="containers",cascade={"persist"})
     * @ORM\JoinColumn(name="shipment_id", referencedColumnName="id")
     */
    private $shipment;

    /**
     * @ORM\OneToMany(targetEntity="ContainerItem", mappedBy="container",cascade={"persist","remove"}))
     */
    private $items;

    /** @ORM\Column(name="tariff_cost",type="string") */
    private $tariffCost;

    /**
     * @ORM\OneToOne(targetEntity="ContainerExtraOwner", mappedBy="container", cascade={"persist"})
     */
    private $extraOwner;

    public function getSum()
    {
        $sum = 0;
        foreach ($this->getItems() as $item) {
            $sum += $item->getSum();
        }
        return $sum;
    }

    public function getWeight()
    {
        $sum = 0;
        foreach ($this->getItems() as $item) {
            $sum += $item->getWeight();
        }
        return $sum;
    }

    public function getRealWeight()
    {
        $sum = 0;
        foreach ($this->getItems() as $item) {
            $sum += $item->getRealWeight();
        }
        return $sum;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * @return \Storage\Entity\Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    public function setTariffCost($cost)
    {
        $this->tariffCost = $cost;
    }

    public function getTariffCost()
    {
        return $this->tariffCost;
    }

    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
            $item->setContainer($this);
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getCountItems()
    {
        return count($this->getItems());
    }

    public function setExtraOwner($extra)
    {
        $this->extraOwner = $extra;
    }

    public function getExtraOwner(): ?ContainerExtraOwner
    {
        return $this->extraOwner;
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
                'name' => 'name',
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
                            'max' => 45,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * This method is necessary for ArraySerializable hydrator
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'name' => $this->getName(),
            'owner' => $this->getExtraOwner() ? $this->getExtraOwner()->getOwner() : null
        ];
    }

    /**
     * This method is necessary for ArraySerializable hydrator
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->setName($data['name']);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'trader' => $this->getShipment()->getTrader()->getName(),
            'tariff' => $this->getShipment()->getTariff()->getName(),
            'shipment_date' => $this->getShipment()->getDate(),
            'container_items' => $this->getItems()->toArray(),
            'date' => $this->getShipment()->getDate(),
            'extraOwner' => $this->getExtraOwner(),
        ];
    }
}
