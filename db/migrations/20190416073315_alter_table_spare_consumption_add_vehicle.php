<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareConsumptionAddVehicle extends AbstractMigration
{
    protected $table = 'spare_consumption';
    protected $column = 'vehicle_id';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            return;
        }

        $table->addColumn($this->column, 'biginteger', [
            'limit' => 20,
            'comment' => 'id техники',
        ])
            ->addForeignKey($this->column, 'vehicle', ['id'])
            ->save();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table->removeColumn($this->column)
            ->save();
    }
}
