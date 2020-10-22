<?php
namespace Storage\Entity;

use Core\Entity\AbstractEntity;
use Core\Entity\EntityWithOptions;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Storage\Repository\ShipmentRepository")
 * @ORM\Table(name="shipment")
 */
class Shipment implements InputFilterAwareInterface, \JsonSerializable, AbstractEntity
{
    use EntityWithOptions;

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
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\Trader")
     * @ORM\JoinColumn(name="trader_id", referencedColumnName="id")
     */
    private $trader;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\ShipmentTariff")
     * @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     */
    private $tariff;

    /** @ORM\Column(name="dollar_rate",type="string") */
    private $rate;

    /** @ORM\Column(type="json") */
    private $options;

    /**
     *
     * @ORM\OneToMany(targetEntity="Container", mappedBy="shipment",cascade={"persist","remove"}))
     */
    private $containers;

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

    public function setDepartment($dep)
    {
        $this->department = $dep;
    }
    public function getDepartment()
    {
        return $this->department;
    }

    public function setTrader($trader)
    {
        $this->trader = $trader;
    }
    public function getTrader()
    {
        return $this->trader;
    }

    public function setTariff($tariff)
    {
        $this->tariff = $tariff;
    }
    public function getTariff()
    {
        return $this->tariff;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }
    public function getRate()
    {
        return $this->rate;
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

    public function addContainers($containers)
    {
        /** @var Container $container */
        foreach ($containers as $container) {
            $this->containers[] = $container;
            $container->setShipment($this);
        }
    }

    public function getContainers()
    {
        return $this->containers;
    }

    public function removeContainers()
    {
        $this->containers = [] ;
    }

    public function getWeight()
    {
        $weight = 0;
        /** @var Container $container */
        foreach ($this->getContainers() as $container) {
            $weight += $container->getWeight();
        }
        return $weight;
    }

    public function getRealWeight()
    {
        $weight = 0;
        foreach ($this->getContainers() as $container) {
            $weight += $container->getRealWeight();
        }
        return $weight;
    }

    public function getSum()
    {
        $sum = 0;
        foreach ($this->getContainers() as $container) {
            $sum += $container->getSum();
        }
        return $sum;
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
                'name'     => 'rate',
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
                            'max'      => 13,
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

            $inputFilter->add([
                'name' => 'factoring',
                'required' => false,
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
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'trader' => $this->getTrader(),
            'sum' => $this->getSum(),
        ];
    }
}
