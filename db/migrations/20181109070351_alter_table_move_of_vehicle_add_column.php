<?php

use Phinx\Migration\AbstractMigration;

class AlterTableMoveOfVehicleAddColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'move_of_vehicles';

    protected $columnDeparture = 'departure';
    protected $columnArrival = 'arrival';
    protected $columnDepartureFromPoint = 'departure_from_point_time';
    protected $columnArrivalAtPoint = 'arrival_at_point_time';
    protected $columnDistance = 'distance';
    protected $columnRemoteSkladId = 'remote_sklad_id';
    protected $columnDepartureTime = 'departure_time';
    protected $columnArrivalTime = 'arrival_time';

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->columnDeparture)) {
            $table->addColumn($this->columnDeparture, 'string', [
                'default' => null,
                'null' => true,
                'comment' => 'Адрес отправления',
            ])
                ->addColumn($this->columnArrival, 'string', [
                    'default' => null,
                    'null' => true,
                    'comment' => 'Адрес назначения',
                ])
                ->addColumn($this->columnDepartureFromPoint, 'datetime', [
                    'default' => null,
                    'null' => true,
                    'comment' => 'Время убытия из точку назначения',
                ])
                ->addColumn($this->columnArrivalAtPoint, 'datetime', [
                    'default' => null,
                    'null' => true,
                    'comment' => 'Время прибытия на точку назначения',
                ])
                ->addColumn($this->columnDistance, 'integer', [
                    'default' => null,
                    'null' => true,
                    'comment' => 'Количество километров, пройденное автомобилем',
                ])
                ->addColumn($this->columnRemoteSkladId, 'integer', [
                    'default' => null,
                    'null' => true,
                ])
                ->changeColumn($this->columnDepartureTime, 'datetime', [
                    'comment' => 'Время выезда с базы',
                    'default' => null,
                    'null' => true,
                ])
                ->changeColumn($this->columnArrivalTime, 'datetime', [
                    'comment' => 'Время возвращения на базу',
                    'default' => null,
                    'null' => true,
                ])
                ->update();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnDeparture)) {
            $table->removeColumn($this->columnDeparture)
                ->removeColumn($this->columnArrival)
                ->removeColumn($this->columnDepartureFromPoint)
                ->removeColumn($this->columnArrivalAtPoint)
                ->removeColumn($this->columnDistance)
                ->removeColumn($this->columnRemoteSkladId)
                ->update();
        }
    }
}
