<?php
use Phinx\Migration\AbstractMigration;

class CreateTableSpareOrderExpenseRef extends AbstractMigration
{
    protected $table = 'spare_order_expense_ref';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('expense_id', 'biginteger', ['limit' => 20])
                ->addColumn('order_id', 'integer',[
                    'limit' => 11
                ])
                ->addForeignKey('expense_id', 'main_other_expenses')
                ->addForeignKey('order_id', 'spare_order')
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
