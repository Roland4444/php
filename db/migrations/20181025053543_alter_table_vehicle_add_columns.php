<?php

use Phinx\Migration\AbstractMigration;

class AlterTableVehicleAddColumns extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'vehicle';

    protected $columnModel = 'model';
    protected $columnNumber = 'number';
    protected $columnSpecialEquipmentConsumption = 'special_equipment_consumption';
    protected $columnEngineConsumption = 'engine_consumption';

    /**
     * Добавление колонок
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->columnModel)) {
            $table->addColumn($this->columnNumber, 'string', [
                    'limit' => 10,
                    'default' => null,
                    'null' => true,
                ])
                ->addColumn($this->columnSpecialEquipmentConsumption, 'decimal', [
                        'scale' => 2, 'precision' => 10,
                        'default' => null,

                    ])
                ->addColumn($this->columnEngineConsumption, 'decimal', [
                        'scale' => 2, 'precision' => 10,
                        'default' => null,
                        'null' => true,
                    ])
                ->addColumn($this->columnModel, 'string', [
                        'limit' => 10,
                        'default' => null,
                        'null' => true,
                    ])
                ->update();
        }
    }

    /**
     * Удаление колонок
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnModel)) {
            $table->removeColumn($this->columnNumber)
                ->removeColumn($this->columnSpecialEquipmentConsumption)
                ->removeColumn($this->columnEngineConsumption)
                ->removeColumn($this->columnModel)
                ->update();
        }
    }
}
