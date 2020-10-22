<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderAddColumnExpectedDate extends AbstractMigration
{
    protected $table = 'spare_order';
    protected $column = 'expected_date';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'date', [ 'comment' => 'Ожидаемая дата поступления'])->save();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->save();
        }
    }
}
