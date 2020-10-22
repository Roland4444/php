<?php
namespace Reference\Entity;

use Core\Entity\EntityWithOptions;
use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Reference\Repositories\CustomerRepository")
 * @ORM\Table(name="customer")
 */
class Customer implements InputFilterAwareInterface, JsonSerializable
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

    /** @ORM\Column(type="string") */
    private string $inn;

    /** @ORM\Column(type="boolean") */
    private $def;

    /** @ORM\Column(type="boolean") */
    private $legal;

    /** @ORM\Column(name="inspection_date", type="string") */
    private $inspectionDate;

    /** @ORM\Column(type="json") */
    private $options;

    /**
     * @return string|null
     */
    public function getInspectionDate()
    {
        return empty($this->inspectionDate) ? null : date('Y-m-d', strtotime($this->inspectionDate));
    }

    /**
     * @param string|null $inspectionDate
     */
    public function setInspectionDate($inspectionDate)
    {
        $this->inspectionDate = empty($inspectionDate) ? null : date('Y-m-d', strtotime($inspectionDate));
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

    /**
     * @return string
     */
    public function getInn(): ?string
    {
        return isset($this->inn) ? $this->inn : null;
    }

    /**
     * @param string $inn
     */
    public function setInn(string $inn): void
    {
        $this->inn = $inn;
    }

    public function setDef($def)
    {
        $this->def = $def;
    }
    public function getDef()
    {
        return $this->def;
    }

    public function setLegal($legal)
    {
        $this->legal = $legal;
    }
    public function getLegal()
    {
        return $this->legal;
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
     * Проверяет, Находится ли поставщик в архиве
     *
     * @return bool
     */
    public function isArchive()
    {
        return $this->hasOption(Options::ARCHIVE);
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'def' => $this->getDef(),
            'value' => $this->id,
            'text' => $this->name
        ];
    }
}
