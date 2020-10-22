<?php

namespace Finance\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

/**
 * @ORM\Entity
 * @ORM\Table(name="expense_shablons")
 */
class Template implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $inn;

    /** @ORM\Column(type="string") */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    public function getInn()
    {
        return $this->inn;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
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
                'name' => 'text',
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
                            'max' => 200,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'inn',
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
                            'max' => 10,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]{1,10}$/i',
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
}
