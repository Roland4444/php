<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareConsuptionAddWarehouse extends AbstractMigration
{
    protected $table = 'spare_consumption';
    protected $column = 'warehouse_id';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            return;
        }

        $table->addColumn($this->column, 'integer', ['limit' => 11])
            ->addForeignKey($this->column, 'warehouse')
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

        $table->dropForeignKey($this->column)
            ->removeIndexByName($this->column)
            ->removeColumn($this->column)
            ->save();
    }
}
