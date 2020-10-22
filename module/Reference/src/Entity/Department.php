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
 * @ORM\Entity(repositoryClass="\Reference\Repositories\DepartmentRepository")
 * @ORM\Table(name="department")
 */
class Department implements InputFilterAwareInterface, JsonSerializable
{
    use EntityWithOptions;

    const TYPE_BLACK = 'black';
    const TYPE_COLOR = 'color';

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string") */
    private $alias;

    /** @ORM\Column(type="string") */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     * @ORM\JoinColumn(name="source_department", referencedColumnName="id")
     */
    private $source;

    /** @ORM\Column(type="json") */
    private $options;

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

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     */
    public function setAlias($alias): void
    {
        $this->alias = $alias;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setSource($s)
    {
        $this->source = $s;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function isBlack()
    {
        return $this->getType() === self::TYPE_BLACK;
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
    public function setOptions($options): void
    {
        $this->options = $options;
    }

    /**
     * Проверяет, закрыт ли департамент
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->hasOption(Options::CLOSED);
    }

    /**
     * Проверяет, скрыт ли департамент
     *
     * @return bool
     */
    public function isHide()
    {
        return $this->hasOption(Options::HIDE);
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
                            'max' => 40,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'alias',
                'required' => false,
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
                            'max' => 40,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'source',
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function exchangeArray($data)
    {
        $id = (isset($data['id'])) ? $data['id'] : null;
        $name = (isset($data['name'])) ? $data['name'] : null;
        $type = (isset($data['type'])) ? $data['type'] : null;
        $source = (isset($data['source'])) ? $data['source'] : null;
        $this->setId($id);
        $this->setName($name);
        $this->setType($type);
        $this->setSource($source);
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->id,
            'text' => $this->name
        ];
    }
}
