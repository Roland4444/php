<?php

use Phinx\Migration\AbstractMigration;

class AlterTableMoveOfVehiclesAddColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'move_of_vehicles';

    protected $columnDriverId = 'driver_id';
    protected $columnId = 'id';
    protected $columnDepartureTime = 'departure_time';
    protected $columnArrivalTime = 'arrival_time';

    protected $tableDrivers = 'drivers';

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->columnDriverId)) {
            $table->addColumn($this->columnDepartureTime, 'datetime', [
                'default' => null,
                'null' => true,
            ])
                ->addColumn($this->columnArrivalTime, 'datetime', [
                    'default' => null,
                    'null' => true,
                ])
                ->addColumn($this->columnDriverId, 'integer', [
                    'limit' => 11,
                    'default' => 1,
                    'null' => false,
                ])
                ->addIndex($this->columnDriverId)
                ->addForeignKey($this->columnDriverId, $this->tableDrivers, ['id'],
                    ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->update();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnDriverId)) {
            $table->removeColumn($this->columnDepartureTime)
                ->dropForeignKey($this->columnDriverId)
                ->removeIndexByName($this->columnDriverId)
                ->removeColumn($this->columnDriverId)
                ->removeColumn($this->columnArrivalTime)
                ->update();
        }
    }
}
