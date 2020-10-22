<?php

use Phinx\Migration\AbstractMigration;

class CreateSpareOrderCashExpenseRef extends AbstractMigration
{
    private $tableName = 'spare_order_cash_expense_ref';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->tableName)) {
            $table = $this->table($this->tableName);

            $table->addColumn('expense_id', 'biginteger', ['limit' => 20])
                ->addColumn('order_id', 'integer',[
                    'limit' => 11
                ])
                ->addForeignKey('expense_id', 'storage_other_expense')
                ->addForeignKey('order_id', 'spare_order')
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->tableName)) {
            $this->dropTable($this->tableName);
        }
    }
}
