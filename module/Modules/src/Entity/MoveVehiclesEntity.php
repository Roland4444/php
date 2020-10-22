<?php

namespace Modules\Entity;

use Doctrine\ORM\Mapping as ORM;
use Reference\Entity\Department;
use Zend\I18n\View\Helper\NumberFormat;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class MoveVehiclesEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="move_of_vehicles")
 */
class MoveVehiclesEntity implements InputFilterAwareInterface, \JsonSerializable
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    /**
     * @ORM\Column(type="string")
     */
    private $waybill;

    /**
     * @ORM\Column(type="decimal")
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Department")
     * @ORM\JoinColumn(name="money_department_id", referencedColumnName="id")
     */
    private $moneyDepartment;

    /**
     * @ORM\Column(type="string")
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Employee")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    private $driver;

    /**
     * @ORM\Column(name="arrival_time", type="string")
     */
    private $arrivalTime;

    /**
     * @ORM\Column(name="departure_time", type="string")
     */
    private $departureTime;

    /**
     * @ORM\Column(type="string")
     */
    private $departure;

    /**
     * @ORM\Column(type="string")
     */
    private $arrival;

    /**
     * @ORM\Column(type="integer")
     */
    private $distance;

    /**
     * @ORM\ManyToOne(targetEntity="\Reports\Entity\RemoteSklad")
     * @ORM\JoinColumn(name="remote_sklad_id", referencedColumnName="id")
     */
    private $remoteSklad;

    /**
     * @ORM\Column(name="departure_from_point_time", type="string")
     */
    private $departureFromPointTime;

    /**
     * @ORM\Column(name="arrival_at_point_time", type="string")
     */
    private $arrivalAtPointTime;

    protected $isIncludeRequiredParams = true;

    /**
     * @return mixed
     */
    public function getDepartureFromPointTime()
    {
        if (empty($this->departureFromPointTime)) {
            return date('H:i');
        }
        return $this->departureFromPointTime;
    }

    /**
     * @param mixed $departureFromPointTime
     */
    public function setDepartureFromPointTime($departureFromPointTime)
    {
        $this->departureFromPointTime = $departureFromPointTime;
    }

    /**
     * @return mixed
     */
    public function getArrivalAtPointTime()
    {
        if (empty($this->arrivalAtPointTime)) {
            return date('H:i');
        }
        return $this->arrivalAtPointTime;
    }

    /**
     * @param mixed $arrivalAtPointTime
     */
    public function setArrivalAtPointTime($arrivalAtPointTime)
    {
        $this->arrivalAtPointTime = $arrivalAtPointTime;
    }


    /**
     * @return \Reports\Entity\RemoteSklad
     */
    public function getRemoteSklad()
    {
        return $this->remoteSklad;
    }

    /**
     * @param mixed $remoteSklad
     */
    public function setRemoteSklad($remoteSklad)
    {
        $this->remoteSklad = $remoteSklad;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @param mixed $departure
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;
    }

    /**
     * @return mixed
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @param mixed $arrival
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;
    }

    /**
     * @return mixed
     */
    public function getArrivalTime()
    {
        if (empty($this->arrivalTime)) {
            return date('H:i');
        }
        return $this->arrivalTime;
    }

    /**
     * @param mixed $arrivalTime
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;
    }

    /**
     * @return mixed
     */
    public function getDepartureTime()
    {
        if (empty($this->departureTime)) {
            return date('H:i');
        }
        return $this->departureTime;
    }

    /**
     * @param mixed $departureTime
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;
    }

    /**
     * @return mixed
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        if (! $this->date) {
            return date('Y-m-d');
        }
        return $this->date;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     */
    public function setMoneyDepartment($department)
    {
        $this->moneyDepartment = $department;
    }

    /**
     * @return mixed
     */
    public function getMoneyDepartment()
    {
        return $this->moneyDepartment;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return \Reference\Entity\Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $waybill
     */
    public function setWaybill($waybill)
    {
        $this->waybill = empty($waybill) ? null : $waybill;
    }

    /**
     * @return mixed
     */
    public function getWaybill()
    {
        return $this->waybill;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function disableRequiredParams()
    {
        $this->isIncludeRequiredParams = false;
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'customer',
                'required' => $this->isIncludeRequiredParams,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 40,
                        ],
                        ],
                    ],
                ]);

            $inputFilter->add([
                'name' => 'comment',
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 1000,
                        ],
                    ],
                ],
            ]);

            if ($this->isIncludeRequiredParams) {
                $inputFilter->add(['name' => 'driver', 'required' => true,]);
            }

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $numberFormat = new NumberFormat();
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'customer' => $this->getCustomer(),
            'money' => $numberFormat($this->getPayment(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, 'ru_RU'),
        ];
    }
}
