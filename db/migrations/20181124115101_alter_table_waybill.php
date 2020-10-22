<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWaybill extends AbstractMigration
{
    const TYPE_TIME = 'time';
    const TYPE_DECIMAL = 'decimal';

    /**
     * @var string название таблицы
     */
    protected $table = 'waybill';

    protected $columnEngineTime = [
        'comment' =>'Время работы двигателя',
        'column' => 'engine_time'
    ];
    protected $columnSpecialEquipmentTime = [
        'comment' =>'Время работы спецоборудования',
        'column' => 'special_equipment_time'
    ];

    /**
     * Изменение колонок при миграции
     *
     */
    public function up()
    {
        $this->alterTable(self::TYPE_TIME);
    }

    /**
     * Изменение колонок при откате миграции
     *
     */
    public function down()
    {
        $this->alterTable(self::TYPE_DECIMAL);
    }

    /**
     * Изменение типа колонок
     *
     * @param string $typeColumn
     */
    protected function alterTable($typeColumn){
        $table = $this->table($this->table);
        $arrayMerge = ['scale' => 2, 'precision' => 10,];
        if ($typeColumn != self::TYPE_DECIMAL) {
            $arrayMerge = [];
        }

        $table->changeColumn($this->columnEngineTime['column'], $typeColumn, array_merge(
            ['comment' => $this->columnEngineTime['comment']],
            $arrayMerge
        ))
            ->changeColumn($this->columnSpecialEquipmentTime['column'], $typeColumn, array_merge(
                ['comment' => $this->columnSpecialEquipmentTime['comment']],
                $arrayMerge
            ))
            ->update();
    }
}
