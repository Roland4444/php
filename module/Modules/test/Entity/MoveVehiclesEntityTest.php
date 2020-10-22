<?php

namespace ModuleTest\Entity;

use Modules\Entity\MoveVehiclesEntity;
use PHPUnit\Framework\TestCase;

class MoveVehiclesEntityTest extends TestCase
{
    /**
     * Проверка получения времени выезда с точки во время завершения выезда.
     */
    public function testGetDepartureFromPointTimeEmptyTime()
    {
        $entity = new MoveVehiclesEntity();

        $expected = strtotime(date('H:i'));
        $resultStamp = strtotime($entity->getDepartureFromPointTime());

        if ($resultStamp == ($expected + 1)) {
            $expected++;
        }

        $this->assertSame($expected, $resultStamp);
    }

    public function testGetDepartureFromPointTimeIssetTime()
    {
        $entity = new MoveVehiclesEntity();
        $entity->setDepartureFromPointTime('12:12');
        $this->assertSame('12:12', $entity->getDepartureFromPointTime());
    }

    /**
     * Проверка времени прибытия на точку во время завершения выезда.
     */
    public function testGetArrivalAtPointTimeEmptyTime()
    {
        $entity = new MoveVehiclesEntity();

        $expected = strtotime(date('H:i'));
        $resultStamp = strtotime($entity->getArrivalAtPointTime());

        if ($resultStamp == ($expected + 1)) {
            $expected++;
        }

        $this->assertSame($expected, $resultStamp);
    }

    /**
     * Проверка времени прибытия на точку во время редактирования.
     */
    public function testGetArrivalAtPointTimeIssetTime()
    {
        $entity = new MoveVehiclesEntity();
        $entity->setArrivalAtPointTime('12:12');
        $this->assertSame('12:12', $entity->getArrivalAtPointTime());
    }

    /**
     * Проверка времени прибытия во время завершения выезда.
     */
    public function testGetArrivalTimeEmptyTime()
    {
        $entity = new MoveVehiclesEntity();

        $expected = strtotime(date('H:i'));
        $resultStamp = strtotime($entity->getArrivalTime());

        if ($resultStamp == ($expected + 1)) {
            $expected++;
        }

        $this->assertSame($expected, $resultStamp);
    }
    /**
     * Проверка времени прибытия во время редактирования.
     */
    public function testGetArrivalTimeIssetTime()
    {
        $entity = new MoveVehiclesEntity();
        $entity->setArrivalTime('12:12');
        $this->assertSame('12:12', $entity->getArrivalTime());
    }

    /**
     * Проверка времени выезда во время завершения выезда.
     */
    public function testGetDepartureTimeEmptyTime()
    {
        $entity = new MoveVehiclesEntity();

        $expected = strtotime(date('H:i'));
        $resultStamp = strtotime($entity->getDepartureTime());

        if ($resultStamp == ($expected + 1)) {
            $expected++;
        }

        $this->assertSame($expected, $resultStamp);
    }

    /**
     * Проверка времени выезда во время редактирования.
     */
    public function testGetDepartureTimeIssetTime()
    {
        $entity = new MoveVehiclesEntity();
        $entity->setDepartureTime('12:12');
        $this->assertSame('12:12', $entity->getDepartureTime());
    }

    /**
     * Проверка даты во время завершения выезда.
     */
    public function testGetDateEmptyDate()
    {
        $entity = new MoveVehiclesEntity();

        $expected = strtotime(date('Y-m-d'));
        $resultStamp = strtotime($entity->getDate());

        if ($resultStamp == ($expected + 1)) {
            $expected++;
        }

        $this->assertSame($expected, $resultStamp);
    }

    /**
     * Проверка даты во время редактирования.
     */
    public function testGetDateIssetDate()
    {
        $entity = new MoveVehiclesEntity();
        $entity->setDate('2000-12-12');
        $this->assertSame('2000-12-12', $entity->getDate());
    }

    /**
     * Проверка сохранения путевого листа.
     *
     * @dataProvider providerGetWaybill
     * @param $waybill
     * @param $expected
     */
    public function testSetWaybill($waybill, $expected)
    {
        $entity = new MoveVehiclesEntity();
        $entity->setWaybill($waybill);

        $this->assertSame($expected, $entity->getWaybill());
    }

    /**
     * Тестовые данные для проверки сохранения накладной.
     *
     * @return array
     */
    public function providerGetWaybill()
    {
        return [
            ['', null],
            [1, 1],
            [0, null],
        ];
    }

    /**
     * Проверка установленных фильтров без исключения параметров
     */
    public function testFiltersWithoutDisableRequiredParams()
    {
        $entity = new MoveVehiclesEntity();
        $this->assertNotEmpty($entity->getInputFilter()->count());
    }
}
