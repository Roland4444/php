<?php

use Phinx\Migration\AbstractMigration;

class AlterTableMoveOfVehiclesEditColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'move_of_vehicles';
    protected $columnDepartureTime = 'departure_time';
    protected $columnArrivalTime = 'arrival_time';
    protected $columnDepartureFromPointTime = 'departure_from_point_time';
    protected $columnArrivalAtPointTime = 'arrival_at_point_time';


    /**
     * Изменение колонок
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->columnDepartureTime, 'time', [
            'comment' => 'Время выезда с базы',
            'default' => null,
            'null' => true,
        ])
            ->changeColumn($this->columnArrivalTime, 'time', [
                'comment' => 'Время возвращения на базу',
                'default' => null,
                'null' => true,
            ])
            ->changeColumn($this->columnDepartureFromPointTime, 'time', [
                'comment' => 'Время убытия из точку назначения',
                'default' => null,
                'null' => true,
            ])
            ->changeColumn($this->columnArrivalAtPointTime, 'time', [
                'comment' => 'Время прибытия на точку назначения',
                'default' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Изменение колонок
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->columnDepartureTime, 'datetime', [
            'comment' => 'Время выезда с базы',
            'default' => null,
            'null' => true,
        ])
            ->changeColumn($this->columnArrivalTime, 'datetime', [
                'comment' => 'Время возвращения на базу',
                'default' => null,
                'null' => true,
            ])
            ->changeColumn($this->columnDepartureFromPointTime, 'datetime', [
                'comment' => 'Время убытия из точку назначения',
                'default' => null,
                'null' => true,
            ])
            ->changeColumn($this->columnArrivalAtPointTime, 'datetime', [
                'comment' => 'Время прибытия на точку назначения',
                'default' => null,
                'null' => true,
            ])
            ->update();
    }
}
