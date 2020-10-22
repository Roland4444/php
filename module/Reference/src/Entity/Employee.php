<?php

namespace Reference\Entity;

use Core\Entity\EntityWithOptions;
use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Reference\Repositories\EmployeeRepository")
 * @ORM\Table(name="employees")
 */
class Employee implements InputFilterAwareInterface, JsonSerializable
{
    use EntityWithOptions;

    protected $inputFilter;

    /** @ORM\Id
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
    private $license;

    /** @ORM\Column(type="json") */
    private $options;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param mixed $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    public function isSpare()
    {
        return $this->hasOption(Options::OPTIONS_SPARE);
    }

    public function isDriver()
    {
        return $this->hasOption(Options::OPTIONS_DRIVER);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(['name' => 'name', 'required' => true]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'license' => $this->license,
        ];
    }
}
