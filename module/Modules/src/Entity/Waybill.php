<?php

namespace Modules\Entity;

use Doctrine\ORM\Mapping as ORM;
use Reference\Entity\Employee;
use Reference\Entity\Vehicle;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class Waybill
 *
 * @ORM\Entity
 * @ORM\Table(name="waybill")
 */
class Waybill implements InputFilterAwareInterface
{

    protected $inputFilter;

    protected $defaultParams = [];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Employee")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    private $driver;

    /**
     * @ORM\Column(name="date_start",type="string")
     */
    private $dateStart;

    /**
     * @ORM\Column(name="date_end",type="string")
     */
    private $dateEnd;

    /**
     * @ORM\Column(name="change_factor",type="decimal")
     */
    private $changeFactor;

    /**
     * @ORM\Column(name="speedometer_start",type="integer")
     */
    private $speedometerStart;

    /**
     * @ORM\Column(name="speedometer_end",type="integer")
     */
    private $speedometerEnd;

    /**
     * @ORM\Column(name="fuel_start",type="integer")
     */
    private $fuelStart;

    /**
     * @ORM\Column(name="fuel_end",type="integer")
     */
    private $fuelEnd;

    /**
     * @ORM\Column(name="special_equipment_time",type="string")
     */
    private $specialEquipmentTime;

    /**
     * @ORM\Column(name="engine_time",type="string")
     */
    private $engineTime;

    /**
     * @ORM\Column(type="string")
     */
    private $license;

    /**
     * @ORM\Column(name="car_number",type="string")
     */
    private $carNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $refueled;

    /**
     * @ORM\Column(name="waybill_number",type="integer")
     */
    private $waybillNumber;

    /**
     * @var @ORM\Column(type="text")
     */
    private $mechanic;

    /**
     * @var @ORM\Column(type="text")
     */
    private $dispatcher;

    /** @ORM\Column(name="fuel_consumption",type="decimal") */
    private $fuelConsumption;

    /**
     * @return string
     */
    public function getMechanic()
    {
        if (empty($this->mechanic)) {
            $this->mechanic = $this->getValueFromDefaultParams(WaybillSettings::MECHANIC);
        }
        return $this->mechanic;
    }

    private function getValueFromDefaultParams($paramName)
    {
        $default = array_filter($this->getDefaultParams(), function ($setting) use ($paramName) {
            return $setting->getName() == $paramName;
        });
        if (! empty($default)) {
            return array_pop($default)->getValue();
        }
        return null;
    }

    /**
     * @param string $mechanic
     */
    public function setMechanic($mechanic)
    {
        $this->mechanic = $mechanic;
    }

    /**
     * @return string
     */
    public function getDispatcher()
    {
        if (empty($this->dispatcher)) {
            $this->dispatcher = $this->getValueFromDefaultParams(WaybillSettings::DISPATCHER);
        }
        return $this->dispatcher;
    }

    /**
     * @param string $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $params
     */
    public function setDefaultParams($params)
    {
        $this->defaultParams = $params;
    }

    protected function getDefaultParams()
    {
        return $this->defaultParams;
    }

    /**
     * @return integer
     */
    public function getWaybillNumber()
    {
        return $this->waybillNumber;
    }

    /**
     * @param integer $waybillNumber
     */
    public function setWaybillNumber($waybillNumber)
    {
        $this->waybillNumber = (int)$waybillNumber;
    }

    /**
     * @return mixed
     */
    public function getRefueled()
    {
        return $this->refueled;
    }

    /**
     * @param mixed $refueled
     */
    public function setRefueled($refueled)
    {
        $this->refueled = $refueled;
    }

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return Employee
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
     * @param string
     * @return mixed
     */
    public function getDateStart($format = 'Y-m-d')
    {
        $time = strtotime($this->dateStart);
        if (empty($this->dateStart)) {
            $time = time();
        }
        return date($format, $time);
    }

    /**
     * @param mixed $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = date('Y-m-d', strtotime($dateStart));
    }

    /**
     * @return mixed
     */
    public function getDateEnd()
    {
        $time = strtotime($this->dateEnd);
        if (empty($this->dateEnd)) {
            $time = time();
        }
        return date('Y-m-d', $time);
    }

    /**
     * @param mixed $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = date('Y-m-d', strtotime($dateEnd));
    }

    /**
     * @return float
     */
    public function getChangeFactor()
    {
        if (empty($this->changeFactor)) {
            $this->changeFactor = $this->getValueFromDefaultParams(WaybillSettings::CHANGE_FACTOR);
        }
        if (empty($this->changeFactor)) {
            return 1;
        }
        return $this->changeFactor;
    }

    /**
     * @param mixed $changeFactor
     */
    public function setChangeFactor($changeFactor)
    {
        $this->changeFactor = $changeFactor;
    }

    /**
     * @return mixed
     */
    public function getSpeedometerStart()
    {
        return $this->speedometerStart;
    }

    /**
     * @param mixed $speedometerStart
     */
    public function setSpeedometerStart($speedometerStart)
    {
        $this->speedometerStart = $speedometerStart;
    }

    /**
     * @return mixed
     */
    public function getSpeedometerEnd()
    {
        return $this->speedometerEnd;
    }

    /**
     * @param mixed $speedometerEnd
     */
    public function setSpeedometerEnd($speedometerEnd)
    {
        $this->speedometerEnd = $speedometerEnd;
    }

    /**
     * @return mixed
     */
    public function getFuelStart()
    {
        return $this->fuelStart;
    }

    /**
     * @param mixed $fuelStart
     */
    public function setFuelStart($fuelStart)
    {
        $this->fuelStart = $fuelStart;
    }

    /**
     * @return mixed
     */
    public function getFuelEnd()
    {
        return $this->fuelEnd;
    }

    /**
     * @param mixed $fuelEnd
     */
    public function setFuelEnd($fuelEnd)
    {
        $this->fuelEnd = $fuelEnd;
    }

    /**
     * @return mixed
     */
    public function getSpecialEquipmentTime()
    {
        return date('H:i', strtotime($this->specialEquipmentTime));
    }

    /**
     * @param mixed $specialEquipmentTime
     */
    public function setSpecialEquipmentTime($specialEquipmentTime)
    {
        $this->specialEquipmentTime = date('H:i', strtotime($specialEquipmentTime));
    }

    /**
     * @return mixed
     */
    public function getEngineTime()
    {
        return date('H:i', strtotime($this->engineTime));
    }

    /**
     * @param mixed $engineTime
     */
    public function setEngineTime($engineTime)
    {
        $this->engineTime = date('H:i', strtotime($engineTime));
    }

    /**
     * @return mixed
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @param mixed $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * @return mixed
     */
    public function getCarNumber()
    {
        return $this->carNumber;
    }

    /**
     * @param mixed $carNumber
     */
    public function setCarNumber($carNumber)
    {
        $this->carNumber = $carNumber;
    }

    /**
     * @return integer
     */
    public function getFuelConsumption()
    {
        return $this->fuelConsumption;
    }

    /**
     * @param integer $fuelConsumption
     */
    public function setFuelConsumption($fuelConsumption)
    {
        $this->fuelConsumption = $fuelConsumption;
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

    /**
     * Расход горючего нормативный
     *
     * @return int
     */
    public function getConsumptionNormal()
    {
        //Норма потребления на 1км
        $consumptionNormal = $this->getChangeFactor() * $this->fuelConsumption / 100;

        //Потребление работой двигателя в движении
        $consumptionRun = ($this->speedometerEnd - $this->speedometerStart) * $consumptionNormal;

        //Потребление работой двигателя при простои с включеном двигателем
        $consumptionEngine = $this->getVehicle()->getEngineConsumption()
            * $this->getHours($this->getEngineTime());

        //Потребление спецоборудованием
        $consumptionSpec = $this->getVehicle()->getSpecialEquipmentConsumption()
            * $this->getHours($this->getSpecialEquipmentTime());

        return round($consumptionRun + $consumptionEngine + $consumptionSpec);
    }

    /**
     * Возвращает время часов
     *
     * @param $time string Время в часах
     * @return integer
     */
    protected function getHours($time)
    {
        $time = new \DateTime($time);
        $hour = (int)$time->format('H');
        $minutes = (int)$time->format('i') / 60;
        return $hour + $minutes;
    }

    /**
     * Расход горючего реальный
     *
     * @return int
     */
    public function getConsumptionReal()
    {
        return ($this->fuelStart + $this->refueled) - $this->fuelEnd;
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(['name' => 'dateStart', 'required' => true]);
            $inputFilter->add(['name' => 'dateEnd', 'required' => true]);
            $inputFilter->add(['name' => 'speedometerStart', 'required' => true]);
            $inputFilter->add(['name' => 'speedometerEnd', 'required' => true]);
            $inputFilter->add(['name' => 'fuelStart', 'required' => true]);
            $inputFilter->add(['name' => 'fuelEnd', 'required' => true]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
