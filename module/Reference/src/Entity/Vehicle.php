<?php
namespace Reference\Entity;

use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use Core\Entity\EntityWithOptions;
use JsonSerializable;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class Vehicle
 * @ORM\Entity(repositoryClass="\Reference\Repositories\VehicleRepository")
 * @ORM\Table(name="vehicle")
 */
class Vehicle implements InputFilterAwareInterface, JsonSerializable
{
    use EntityWithOptions;

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /** @ORM\Column(type="string") */
    private $number;

    /** @ORM\Column(name="special_equipment_consumption",type="decimal") */
    private $specialEquipmentConsumption;

    /** @ORM\Column(name="engine_consumption",type="decimal") */
    private $engineConsumption;

    /** @ORM\Column(type="string") */
    private $model;

    /** @ORM\Column(name="fuel_consumption",type="string") */
    private $fuelConsumption;

    /** @ORM\Column(name="options",type="json") */
    private $options;

    /**
     * @return mixed
     */
    public function getFuelConsumption()
    {
        return $this->fuelConsumption;
    }

    /**
     * @param mixed $fuelConsumption
     */
    public function setFuelConsumption($fuelConsumption)
    {
        $this->fuelConsumption = $fuelConsumption;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getSpecialEquipmentConsumption()
    {
        return $this->specialEquipmentConsumption;
    }

    /**
     * @param mixed $specialEquipmentConsumption
     */
    public function setSpecialEquipmentConsumption($specialEquipmentConsumption)
    {
        $this->specialEquipmentConsumption = $specialEquipmentConsumption;
    }

    /**
     * @return mixed
     */
    public function getEngineConsumption()
    {
        return $this->engineConsumption;
    }

    /**
     * @param mixed $engineConsumption
     */
    public function setEngineConsumption($engineConsumption)
    {
        $this->engineConsumption = $engineConsumption;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
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

    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Проверяет является ли техника передвижаной
     *
     * @return bool
     */
    public function isMovable()
    {
        return $this->hasOption(Options::MOVABLE);
    }

    /**
     * Проверяет находится ли техника в архиве
     *
     * @return bool
     */
    public function isArchived()
    {
        return $this->hasOption(Options::ARCHIVAL);
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

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'value' => $this->id,
            'text' => $this->name,
        ];
    }
}
