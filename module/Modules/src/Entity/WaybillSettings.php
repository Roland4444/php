<?php

namespace Modules\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class Waybill
 *
 * @ORM\Entity
 * @ORM\Table(name="waybill_settings")
 */
class WaybillSettings implements InputFilterAwareInterface
{
    const MECHANIC = 'mechanic';
    const DISPATCHER = 'dispatcher';
    const CHANGE_FACTOR = 'changeFactor';

    const SETTINGS = [self::MECHANIC, self::DISPATCHER, self::CHANGE_FACTOR];

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    /**
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
            $inputFilter->add(['name' => 'name', 'required' => true]);
            $inputFilter->add(['name' => 'value', 'required' => true]);
            $inputFilter->add(['name' => 'label', 'required' => true]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
