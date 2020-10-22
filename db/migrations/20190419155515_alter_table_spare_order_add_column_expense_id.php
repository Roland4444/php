<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderAddColumnExpenseId extends AbstractMigration
{
    protected $table = 'spare_order';
    protected $column = 'expense_id';

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
            'comment' => 'id платежа',
            'default' => null,
            'null' => true,
        ])
            ->addForeignKey($this->column, 'main_other_expenses')
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
            ->removeColumn($this->column)
            ->save();
    }
}
