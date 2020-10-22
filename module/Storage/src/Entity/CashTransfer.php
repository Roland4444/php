<?php
namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="storage_cash_transfer")
 */
class CashTransfer implements InputFilterAwareInterface
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
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

    /** @ORM\Column(type="string") */
    private $money;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
    public function getDate()
    {
        if (! $this->date) {
            return date('Y-m-d');
        }
        return $this->date;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }
    public function getSource()
    {
        return $this->source;
    }

    public function setDest($dest)
    {
        $this->dest = $dest;
    }
    public function getDest()
    {
        return $this->dest;
    }

    public function setMoney($money)
    {
        $this->money = $money;
    }
    public function getMoney()
    {
        return $this->money;
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
                'name'     => 'date',
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
                ],
            ]);

            $inputFilter->add([
                'name'     => 'money',
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
                                \Zend\Validator\Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
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
