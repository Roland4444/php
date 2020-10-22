<?php
namespace Spare\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\SpareRepository")
 * @ORM\Table(name="spares")
 */
class Spare implements InputFilterAwareInterface, JsonSerializable
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="integer", name="is_composite") */
    private $isComposite;

    /** @ORM\Column(type="string") */
    private $comment;

    /** @ORM\Column(type="string") */
    private $units;

    private $images;

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
    public function getIsComposite()
    {
        return $this->isComposite;
    }

    /**
     * @param mixed $isComposite
     */
    public function setIsComposite($isComposite)
    {
        $this->isComposite = $isComposite;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getUnits()
    {
        return empty($this->units) ? 'шт.' : $this->units;
    }

    /**
     * @param mixed $units
     */
    public function setUnits($units): void
    {
        $this->units = $units;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @param mixed $image
     */
    public function addImage($image): void
    {
        $this->images[] = $image;
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
                            'max'      => 100,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'isComposite',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'InArray',
                        'options' => [
                            'haystack' => [1, 0],
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'comment' => empty($this->getComment()) ? '' : $this->getComment(),
            'composite' => $this->getIsComposite(),
            'units' => $this->getUnits()
        ];
    }

    public function jsonSerialize()
    {
        return [
            'value' => $this->id,
            'text' => $this->name,
            'isComposite' => $this->isComposite,
            'spareUnits' => $this->units,
        ];
    }
}
