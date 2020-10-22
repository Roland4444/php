<?php
/**
 * Created by PhpStorm.
 * User: kostyrenko
 * Date: 08.11.2018
 * Time: 13:55
 */

namespace Modules\Service;

use Modules\Entity\Waybill;
use Reports\Service\RemoteSkladService;

class WaybillsFill
{
    const DIR_THIS_CLASS = 'src/Service';
    const DIR_DOP_PATH_TO_TEMPLATE = 'view/modules/waybills/waybill.html';
    const PAGE_BREAK = '<div class="page_break"></div>';

    /**
     * Коеффицент умножения растояния в заездах
     */
    const COEFFICIENT_DISTANCE = 2;

    /**
     * Полное название организации
     */
    const FULL_NAME_COMPANY = 'Общество с ограниченной ответственностью "Астрвторсырье", 414040, 
                Астраханская обл. Астрахань г, Чугунова ул, дом 18, корпус 2, помещение 5, тел. (8512) 52-27-66';

    /**
     * Шаблон для таблицы Задание Водителю
     */
    const DRIVER_TASK = '<tr class="tal">
                            <td>{companyName}</td>
                            <td class="tac">{__startDate}</td>
                            <td class="tac">{_pointStart}</td>
                            <td class="tac">{___pointEnd}</td>
                            <td class="tac">{______cargo}</td>
                            <td class="tar">1</td>
                            <td class="tar">{___distance}</td>
                            <td class="tar">{______massa}</td>
                        </tr>';

    const TASK_LIST = '<tr>
                        <td>{_pointStart}</td>
                        <td>{_numberTrip}</td>
                        <td>{____endDate}</td>
                        <td>{__H_endDate}</td>
                        <td>{__i_endDate}</td>
                        <td>{__startDate}</td>
                        <td>{H_startDate}</td>
                        <td>{i_startDate}</td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td>{____company}</td>
                        <td> </td>
                    </tr>';

    /**
     * @var int Id путевого листа
     */
    protected $idWaybill;

    /**
     * @var Waybill Путевой лист
     */
    protected $waybill;

    /**
     * @var string Номер
     */
    protected $number = '';

    /**
     * @var string Марка автомобиля
     */
    protected $markAuto = '';

    /**
     * @var string Гос номер
     */
    protected $numberAuto = '';

    /**
     * @var string Водитель
     */
    protected $driver = '';

    /**
     * @var string Номер водительского удостоверения
     */
    protected $driverLicense = '';

    /**
     * @var \DateTime Дата начала путевого листа
     */
    protected $dateStart;

    /**
     * @var \DateTime Дата конца путевого листа
     */
    protected $dateEnd;

    /**
     * @var int Начальные показания спидометра
     */
    protected $speedometerStart = 0;

    /**
     * @var int Конечныее показания спидометра
     */
    protected $speedometerEnd = 0;

    /**
     * @var int Количество топлива при выезде
     */
    protected $fuelStart = 0;

    /**
     * @var int Количество топлива при возвращении
     */
    protected $fuelEnd = 0;

    /**
     * @var int Вычисляемое поле. Количество поездок
     */
    protected $countTrip = 0;

    /**
     * @var int Вычисляемое поле. Растояние в километрах для таблицы Задание водителю
     */
    protected $distance = 0;

    /**
     * @var int Вычисляемое поле. Общее пройденное растояние
     */
    protected $distanceTotal = 0;

    /**
     * @var int Вычисляемое поле. Общий вес перевозимого груза
     */
    protected $weight = 0;

    /**
     * @var int Вычисляемое поле. Номер строки в последовательности выполнения задания
     */
    protected $numberRowTaskList = 1;
    /**
     * @var int Количество дозоправленного топлива
     */
    protected $refueled;

    /**
     * @var string Наполняемая информация по шаблону для таблицы Последовательность выполнения задания
     */
    protected $taskList = '';

    /**
     * @var string Наполняемая информация по шаблону для таблицы Задание водителю
     */
    protected $driversTasks = '';

    /**
     * @var string Время работы двигателя на холостом ходу
     */
    protected $engineTime;

    /**
     * @var string Время работы спецоборудования
     */
    protected $SPEngineTime;

    /**
     * @var int Количество дней работы техники
     */
    protected $daysWork;

    /**
     * @var int Вычисляемое поле. Нулевой пробег
     */
    protected $zeroRun = 0;

    /**
     * @var array Вспомогательная переменная для сохранения информации о пункте прибытия автомобиля
     */
    protected $endLine = [
        'dmY' => '',
        'H' => '',
        'i' => '',
        'departure' => '',
    ];

    /**
     * @var CompletedVehicleTripsService
     */
    private $completedVehicleTripsService;

    /**
     * @var WaybillsService
     */
    private $waybillsService;
    /**
     * @var RemoteSkladService
     */
    private $remoteSkladService;

    /**
     * @var array [\Modules\Entity\MoveVehiclesEntity] Передвижения техники
     */
    private $trips;


    /**
     * WaybillsFill constructor.
     * @param $id int
     * @param $container \Zend\ServiceManager\ServiceManager
     */
    public function __construct($id, $container)
    {
        $this->idWaybill = $id;
        $this->completedVehicleTripsService = $container[CompletedVehicleTripsService::class];
        $this->waybillsService = $container[WaybillsService::class];
        $this->remoteSkladService = $container[RemoteSkladService::class];
    }

    /**
     * Возвращает готовый html для преобразование в pdf
     *
     * @return string
     * @throws \Exception
     */
    public function getRenderHtml()
    {

        $this->waybill = $this->waybillsService->find($this->idWaybill);

        if (! $this->waybill) {
            throw new \Exception('Ненайден путевой лист');
        }

        $this->trips = $this->completedVehicleTripsService->findTripsForWaybill([
            'startDate' => $this->waybill->getDateStart(),
            'endDate' => $this->waybill->getDateEnd(),
            'driver' => $this->waybill->getDriver()->getId(),
            'vehicle' => $this->waybill->getVehicle()->getId(),
        ]);

        if (empty($this->trips)) {
            throw new \Exception('Не найдено подходящих рейсов');
        }

        $this->fillingDate();

        return strtr($this->getTemplate(), $this->getReplaceData());
    }

    /**
     * Возвращает шаблон для подстановки данных
     *
     * @return bool|string
     */
    protected function getTemplate()
    {
        $dir = __DIR__;
        $dir = str_replace(self::DIR_THIS_CLASS, '', $dir) . self::DIR_DOP_PATH_TO_TEMPLATE;
        return file_get_contents($dir);
    }

    /**
     * Наполняет данные объекта
     */
    protected function fillingDate()
    {
        $this->renderTrips();
        $waybill = $this->waybill;

        $this->number = $waybill->getWaybillNumber();

        if (empty($waybill->getVehicle()->getModel()) || empty($waybill->getVehicle()->getNumber())) {
            $this->markAuto = $waybill->getVehicle()->getName();
        } else {
            $this->markAuto = $waybill->getVehicle()->getModel();
        }

        $this->numberAuto = $waybill->getVehicle()->getNumber();
        $this->driver = $waybill->getDriver()->getName();
        $this->driverLicense = $waybill->getLicense();
        $this->speedometerStart = $waybill->getSpeedometerStart();
        $this->speedometerEnd = $waybill->getSpeedometerEnd();
        $this->fuelStart = $waybill->getFuelStart();
        $this->fuelEnd = $waybill->getFuelEnd();
        $this->countTrip = count($this->trips);
        $this->refueled = $waybill->getRefueled();
        $this->engineTime = $waybill->getEngineTime();
        $this->SPEngineTime = $waybill->getSpecialEquipmentTime();

        $this->dateStart = new \DateTime($this->dateStart);
        $this->dateEnd = new \DateTime($this->dateEnd);

        $this->zeroRun = $waybill->getSpeedometerEnd() - $waybill->getSpeedometerStart() - $this->distanceTotal;

        $this->daysWork = $this->dateStart->diff($this->dateEnd)->days + 1;
    }

    /**
     * Возвращает массив для замены данных в шаблоне
     *
     * @return array
     */
    protected function getReplaceData()
    {
        return [
            // Первый лист шапка
            '{_______number}' => $this->number,
            '{__nameCompany}' => self::FULL_NAME_COMPANY,
            '{____dateStart}' => $this->dateStart->format('d.m.Y'),
            '{______dateEnd}' => $this->dateEnd->format('d.m.Y'),

            //Первый лист, первая линия левый блок
            '{_____markAuto}' => $this->markAuto,
            '{_____numbAuto}' => $this->numberAuto,
            '{_______driver}' => $this->driver,
            '{driverLicense}' => $this->driverLicense,

            //Первый лист, первая линия правый блок
            '{____dStart___}' => $this->dateStart->format('d'),
            '{____mStart___}' => $this->dateStart->format('m'),
            '{____HStart___}' => $this->dateStart->format('H'),
            '{____iStart___}' => $this->dateStart->format('i'),
            '{______zeroRun}' => $this->zeroRun,
            '{___speedStart}' => $this->speedometerStart,
            '{___dateTStart}' => $this->dateStart->format('d.m, H:i'),

            '{______dEnd___}' => $this->dateEnd->format('d'),
            '{______mEnd___}' => $this->dateEnd->format('m'),
            '{______HEnd___}' => $this->dateEnd->format('H'),
            '{______iEnd___}' => $this->dateEnd->format('i'),
            '{_____speedEnd}' => $this->speedometerEnd,
            '{_____dateTEnd}' => $this->dateEnd->format('d.m, H:i'),

            //Движение горючего
            '{____fuelStart}' => $this->fuelStart,
            '{______fuelEnd}' => $this->fuelEnd,
            '{_____refueled}' => $this->refueled,

            '{_changeFactor}' => $this->getChangeFactor(),

            '{___engineTime}' => $this->engineTime,
            '{_SPEngineTime}' => $this->SPEngineTime,


            //Заполнение таблицы Задание Водителю
            '{__driversTask}' => $this->driversTasks,
            '{____countTrip}' => $this->countTrip,
            '{_____distance}' => $this->distance,
            '{__totalWeight}' => $this->weight,

            //Подписи
            '{___dispatcher}' => $this->waybill->getDispatcher(),
            '{_____mechanic}' => $this->waybill->getMechanic(),

            //Разрыв страницы
            '{____pageBreak}' => $this->countTrip >= 4 ? self::PAGE_BREAK : '',

            //Заполнение таблицы Послеловательность выполнения задания
            '{_____taskList}' => $this->taskList,

            '{_consumptionR}' => $this->waybill->getConsumptionReal(),
            '{_consumptionN}' => $this->waybill->getConsumptionNormal(),
            '{totalDistance}' => $this->speedometerEnd - $this->speedometerStart,

            //крайняя строка
            '{_____daysWork}' => $this->daysWork,
        ];
    }

    /**
     * Обработка поездок
     *
     */
    protected function renderTrips()
    {
        foreach ($this->trips as $trip) {
            if (empty($this->dateStart)) {
                $this->dateStart = $trip->getDate() . ' ' . $trip->getDepartureTime();
            }
            $this->dateEnd = $trip->getDate() . ' ' . $trip->getArrivalTime();

            $this->setRowDriversTask($trip);
            $this->setRowTaskList($trip);
        }

        $this->setRowTaskList(null);
    }

    /**
     * Добавляет строку для таблицы Задание водителю
     *
     * @param $trip \Modules\Entity\MoveVehiclesEntity
     */
    protected function setRowDriversTask($trip)
    {
        $weight = ! empty($trip->getRemoteSklad()) ? $trip->getRemoteSklad()->getMassa() : 0;
        $driverTask = self::DRIVER_TASK;
        $replaceDate = [
            '{_pointStart}' => $trip->getDeparture(),
            '{__startDate}' => date('H:i', strtotime($trip->getArrivalAtPointTime())),
            '{___pointEnd}' => $trip->getArrival(),
            '{companyName}' => self::FULL_NAME_COMPANY,
            '{______cargo}' => ! empty($trip->getRemoteSklad()) ? $trip->getRemoteSklad()->getGruz() : '',
            '{___distance}' => $trip->getDistance(),
            '{______massa}' => $weight
        ];

        $this->distance += $trip->getDistance();
        $this->distanceTotal += $trip->getDistance() * self::COEFFICIENT_DISTANCE;
        $this->weight += $weight;
        $this->driversTasks .= strtr($driverTask, $replaceDate);
    }

    /**
     * Добавляет строку для таблицы Последовательность выполнения задания
     *
     * @param $trip \Modules\Entity\MoveVehiclesEntity
     */
    protected function setRowTaskList($trip)
    {
        $numberLine = 0;
        for ($i = 0; $i < 2; $i++) {
            $taskList = self::TASK_LIST;
            $replaceDate = [
                '{_pointStart}' => $this->getPointStarForRowTaskList($trip, $i),
                '{_numberTrip}' => $trip ? $this->numberRowTaskList++ : ' ',
                '{____endDate}' => $this->endLine['dmY'],
                '{__H_endDate}' => $this->endLine['H'],
                '{__i_endDate}' => $this->endLine['i'],
                '{__startDate}' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'd.m.Y'),
                '{H_startDate}' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'H'),
                '{i_startDate}' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'i'),
                '{____company}' => $trip ? 'АВС' : '',
            ];

            $taskList = strtr($taskList, $replaceDate);
            $this->taskList .= $taskList;

            if (! $trip) {
                break;
            }
            $numberLine++;
            $this->endLine = [
                'dmY' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'd.m.Y'),
                'H' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'H'),
                'i' => $this->getFormatDateForRowTaskList($trip, $numberLine, 'i'),
                'departure' => $trip->getArrival(),
                'number' => $numberLine,
            ];
            $numberLine++;
        }
    }

    /**
     * Возвращает пункт погрузки/разгрузки
     *
     * @param \Modules\Entity\MoveVehiclesEntity $trip
     * @param int $i Номер строки
     * @return string $pointStart
     */
    protected function getPointStarForRowTaskList($trip, $i)
    {
        if (! $trip) {
            $pointStart = $this->endLine['departure'];
        } else {
            $pointStart = $i == 0 ? $trip->getArrival() : $trip->getDeparture();
        }

        return $pointStart;
    }

    /**
     * Возвращает дату погрузки/разгрузки
     *
     * @param \Modules\Entity\MoveVehiclesEntity $trip
     * @param int $i Номер заполнения
     * @param string $format Формат даты
     * @return string $pointStart
     */
    protected function getFormatDateForRowTaskList($trip, $i, $format)
    {
        if (! $trip) {
            $pointStart = ' ';
        } else {
            switch ($i) {
                case 0:
                    $pointStart = $trip->getDate(). ' ' . $trip->getDepartureTime();
                    break;
                case 1:
                    $pointStart = $trip->getDate(). ' ' . $trip->getArrivalAtPointTime();
                    break;
                case 2:
                    $pointStart = $trip->getDate(). ' ' . $trip->getDepartureFromPointTime();
                    break;
                case 3:
                    $pointStart = $trip->getDate(). ' ' . $trip->getArrivalTime();
                    break;
            }

            $pointStart = date($format, strtotime($pointStart));
        }

        return $pointStart;
    }

    /**
     * Удаляет из конца коэффицента 0
     *
     * @return string
     */
    protected function getChangeFactor()
    {
        $factor = (string)$this->waybill->getChangeFactor();
        for ($i = strlen($factor) - 1; $i > 0; $i--) {
            if ($factor[$i] != '0' && $factor[$i] != '.' && $factor[$i] != ',') {
                break;
            }
            $factor[$i] = ' ';
        }

        return trim($factor);
    }
}
