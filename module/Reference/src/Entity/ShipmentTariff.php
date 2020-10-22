<?php
namespace Reference\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="tariff_shipment")
 */
class ShipmentTariff implements InputFilterAwareInterface
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string") */
    private $destination;

    /** @ORM\Column(type="string") */
    private $distance;

    /** @ORM\Column(type="integer") */
    private $def;

    /** @ORM\Column(type="string") */
    private $type;

    /** @ORM\Column(type="string") */
    private $money;

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

    public function setDestination($dest)
    {
        $this->destination = $dest;
    }
    public function getDestination()
    {
        return $this->destination;
    }

    public function setDistance($distance)
    {
        $this->distance = $distance;
    }
    public function getDistance()
    {
        return $this->distance;
    }

    public function setDef($def)
    {
        $this->def = $def;
    }
    public function getDef()
    {
        return $this->def;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    public function getType()
    {
        return $this->type;
    }

    public function setMoney($money)
    {
        $this->money = $money;
    }
    public function getMoney()
    {
        return $this->money;
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
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 40,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'destination',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 40,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
