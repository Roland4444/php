<?php

namespace Storage\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Reference\Entity\Department;
use Reference\Entity\Metal;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Date;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\TransferRepository")
 * @ORM\Table(name="transfer")
 */
class Transfer implements AbstractEntity, InputFilterAwareInterface
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="date") */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Department")
     * @ORM\JoinColumn(name="source_department_id", referencedColumnName="id")
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Department")
     * @ORM\JoinColumn(name="dest_department_id", referencedColumnName="id")
     */
    private $dest;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Metal")
     * @ORM\JoinColumn(name="metal_id", referencedColumnName="id")
     */
    private $metal;

    /** @ORM\Column(type="string") */
    private $weight;

    /** @ORM\Column(name="actual_weight",type="string") */
    private $actual;

    private $nakl1;

    private $nakl2;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param Department $source
     */
    public function setSource(Department $source)
    {
        $this->source = $source;
    }

    /**
     * @return Department
     */
    public function getSource(): ?Department
    {
        return $this->source;
    }

    /**
     * @param Department $dest
     */
    public function setDest(Department $dest)
    {
        $this->dest = $dest;
    }

    /**
     * @return Department
     */
    public function getDest(): ?Department
    {
        return $this->dest;
    }

    /**
     * @param Metal $metal
     */
    public function setMetal(Metal $metal)
    {
        $this->metal = $metal;
    }

    /**
     * @return Metal
     */
    public function getMetal(): ?Metal
    {
        return $this->metal;
    }

    public function setWeight($w)
    {
        $this->weight = $w;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setActual($w)
    {
        $this->actual = $w;
    }

    public function getActual()
    {
        return $this->actual;
    }

    public function setNakl1($n)
    {
        $this->nakl1 = $n;
    }

    public function getNakl1()
    {
        return $this->nakl1;
    }

    public function setNakl2($n)
    {
        $this->nakl2 = $n;
    }

    public function getNakl2()
    {
        return $this->nakl2;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $validator = new Date();
            $validator->setFormat('Y-m-d');

            $inputFilter->add([
                'name' => 'date',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    $validator
                ],
            ]);

            $inputFilter->add([
                'name' => 'weight',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'Float',
                        'options' => [
                            'min' => 0,
                            'locale' => 'en_EN'
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'actual',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'Float',
                        'options' => [
                            'min' => 0,
                            'locale' => 'en_EN'
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
