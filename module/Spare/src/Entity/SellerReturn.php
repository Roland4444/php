<?php

namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\SellerReturnsRepository")
 * @ORM\Table(name="spare_seller_returns")
 */
class SellerReturn implements JsonSerializable, AbstractEntity, InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $date;


    /** @ORM\Column(type="decimal") */
    private $money;

    /**
     * @ORM\ManyToOne(targetEntity="Spare\Entity\Seller")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param mixed $money
     */
    public function setMoney($money): void
    {
        $this->money = (float)$money;
    }

    /**
     * @return mixed
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param mixed $seller
     */
    public function setSeller($seller): void
    {
        $this->seller = $seller;
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'date',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'money',
                'required' => true,
                'filters' => [
                    ['name' => 'ToFloat']
                ],
                'validators' => [
                    [
                        'name' => 'float',
                    ],
                    [
                        'name' => 'GreaterThan',
                        'options' => [
                            'min' => 0,
                        ]
                    ]
                ],
            ]);

            $inputFilter->add([
                'name' => 'seller',
                'required' => true,
                'validators' => [
                    [
                        'name' => 'int',
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'money' => $this->money,
            'seller' => $this->getSeller()
        ];
    }

    /**
     * @inheritDoc
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}
