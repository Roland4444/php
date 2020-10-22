<?php

use Phinx\Migration\AbstractMigration;

class CreateTableWaybill extends AbstractMigration
{
    protected $table = 'waybill';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('vehicle_id', 'biginteger', ['limit' => 20])
                ->addColumn('driver_id', 'integer', ['limit' => 11])
                ->addColumn('date_start', 'date', ['comment' => 'Время выезда'])
                ->addColumn('date_end', 'date', ['comment' => 'Время прибытия'])
                ->addColumn('change_factor', 'decimal', [
                    'comment' => 'Коэф изменения нормы',
                    'scale' => 2, 'precision' => 10,
                    'default' => 1,
                ])
                ->addColumn('speedometer_start', 'integer', [
                    'limit' => 9,
                    'comment' => 'Показание спидометра при выезде',
                ])
                ->addColumn('speedometer_end', 'integer', [
                    'limit' => 9,
                    'comment' => 'Показание спидометра при возвращении',
                ])
                ->addColumn('fuel_start', 'integer', [
                    'limit' => 6,
                    'comment' => 'Остаток топлива при выезде',
                ])
                ->addColumn('fuel_end', 'integer', [
                    'limit' => 6,
                    'comment' => 'Остаток топлива при возвращении',
                ])
                ->addColumn('refueled', 'integer', [
                    'default' => 0,
                    'limit' => 6,
                    'comment' => 'Дозаправлено/выдано топлива',
                ])
                ->addColumn('special_equipment_time', 'decimal', [
                    'comment' => 'Время работы спецоборудования',
                    'scale' => 2, 'precision' => 10,
                ])
                ->addColumn('engine_time', 'decimal', [
                    'comment' => 'Время работы двигателя',
                    'scale' => 2, 'precision' => 10,
                ])
                ->addColumn('license', 'string', [
                    'limit' => 14,
                    'default' => null,
                    'null' => true,
                    'comment' => 'Номер водительского удостоверения',
                ])
                ->addColumn('car_number', 'string', [
                    'limit' => 10,
                    'default' => null,
                    'null' => true,
                    'comment' => 'Номер автомобиля',
                ])
                ->addIndex('vehicle_id')
                ->addIndex('driver_id')
                ->addForeignKey('vehicle_id', 'vehicle', ['id'],
                    ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('driver_id', 'drivers', ['id'],
                    ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->table)) {
            $this->dropTable($this->table);
        }
    }
}
