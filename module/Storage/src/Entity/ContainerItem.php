<?php
namespace Storage\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Reference\Entity\Metal;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\ContainerItemRepository")
 * @ORM\Table(name="container_items")
 */
class ContainerItem implements InputFilterAwareInterface, JsonSerializable, AbstractEntity
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Container", inversedBy="items",cascade={"persist"})
     * @ORM\JoinColumn(name="container_id", referencedColumnName="id")
     */
    private $container;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Metal")
     * @ORM\JoinColumn(name="metal_id", referencedColumnName="id")
     */
    private $metal;

    /** @ORM\Column(type="string") */
    private $weight;

    /** @ORM\Column(name="real_weight",type="string") */
    private $realWeight;

    /** @ORM\Column(type="string") */
    private $cost;

    /** @ORM\Column(type="string") */
    private $comment;

    /** @ORM\Column(name="cost_dol",type="string") */
    private $costDol;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function getSum()
    {
        if ($this->getCostDol() > 0) {
            return ($this->getCostDol() * $this->getContainer()->getShipment()->getRate() * $this->getRealWeight()) / 1000;
        } elseif ($this->getCost() > 0) {
            return ($this->getCost() * $this->getRealWeight()) / 1000;
        } else {
            return 0;
        }
    }

    public function setContainer($c)
    {
        $this->container = $c;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function setMetal($m)
    {
        $this->metal = $m;
    }
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

    public function setRealWeight($w)
    {
        $this->realWeight = $w;
    }
    public function getRealWeight()
    {
        return $this->realWeight;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }
    public function getCost()
    {
        if ($this->getCostDol() > 0) {
            $rate = $this->getContainer()->getShipment()->getRate();
            if (! empty($rate)) {
                return $this->getCostDol() * $this->getContainer()->getShipment()->getRate();
            }
        }
        return $this->cost;
    }

    public function setCostDol($cost)
    {
        $this->costDol = $cost;
    }
    public function getCostDol()
    {
        return $this->costDol;
    }

    public function setComment($c)
    {
        $this->comment = $c;
    }
    public function getComment()
    {
        return $this->comment;
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
                'name'     => 'weight',
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
                            'max'      => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,10}$/i',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'realWeight',
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
                            'max'      => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,10}$/i',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'cost',
                'required' => false,
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
                            'max'      => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,10}$/i',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'costDol',
                'required' => false,
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
                            'max'      => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9.]{1,10}$/i',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'comment',
                'required' => false,
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
                            'max'      => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Zа-яА-Я0-9.,% \/\*()\-@"]{1,255}$/ui',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
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
            'name' => $this->getMetal()->getName(),
            'code' => $this->getMetal()->getCode(),
            'alias' => $this->getMetal()->getAlias(),
            'quantity' => $this->weight,
            'price' => $this->cost
        ];
    }
}
