<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Reference\Entity\Metal;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\WeighingItemRepository")
 * @ORM\Table(name="weighing_items")
 */
class WeighingItem implements InputFilterAwareInterface, JsonSerializable
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="decimal")
     */
    private $trash;

    /**
     * @ORM\Column(type="decimal")
     */
    private $clogging;

    /**
     * @ORM\Column(type="decimal")
     */
    private $tare;

    /**
     * @ORM\Column(type="decimal")
     */
    private $brutto;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Metal")
     * @ORM\JoinColumn(name="metal_id", referencedColumnName="id")
     */
    private $metal;

    /**
     * @ORM\ManyToOne(targetEntity="\Storage\Entity\Weighing")
     * @ORM\JoinColumn(name="weighing_id", referencedColumnName="id")
     */
    private $weighing;

    /**
     * @ORM\Column(type="decimal")
     */
    private $price;

    private $photoPreview;

    private $photoFull;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTrash()
    {
        return $this->trash;
    }

    public function setTrash($trash): void
    {
        $this->trash = $trash;
    }

    public function getClogging()
    {
        return $this->clogging;
    }

    public function setClogging($clogging): void
    {
        $this->clogging = $clogging;
    }

    public function getTare()
    {
        return $this->tare;
    }

    public function setTare($tare): void
    {
        $this->tare = $tare;
    }

    public function getBrutto()
    {
        return $this->brutto;
    }

    public function setBrutto($brutto): void
    {
        $this->brutto = $brutto;
    }

    public function getMetal(): Metal
    {
        return $this->metal;
    }

    public function setMetal($metal): void
    {
        $this->metal = $metal;
    }

    public function getWeighing(): Weighing
    {
        return $this->weighing;
    }

    public function setWeighing($weighing): void
    {
        $this->weighing = $weighing;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getPhotoPreview()
    {
        return $this->photoPreview;
    }

    public function setPhotoPreview($photoPreview): void
    {
        $this->photoPreview = $photoPreview;
    }

    public function getPhotoFull()
    {
        return $this->photoFull;
    }

    public function setPhotoFull($photoFull): void
    {
        $this->photoFull = $photoFull;
    }

    public function getMass(): float
    {
        $mass = $this->brutto - $this->tare - $this->trash;
        return round($mass * (1 - $this->clogging / 100), 2);
    }

    /**
     * Сумма взвешивания
     */
    public function getTotalPrice()
    {
        return round($this->getPrice() * $this->getMass(), 2);
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'trash',
                'required' => true,
            ]);

            $inputFilter->add([
                'name' => 'clogging',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Float',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'metalId',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Digits',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'tare',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Float',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'brutto',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Float',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'photoPreview',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'photoFull',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'price',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'trash' => $this->trash,
            'clogging' => $this->clogging,
            'tare' => $this->tare,
            'brutto' => $this->brutto,
            'metal' => $this->getMetal(),
            'metalId' => $this->getMetal()->getId(),
            'mass' => $this->getMass(),
            'price' => $this->getPrice(),
            'totalPrice' => $this->getTotalPrice()
        ];
    }
}
