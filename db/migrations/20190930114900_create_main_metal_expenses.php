<?php

use Phinx\Migration\AbstractMigration;

class CreateMainMetalExpenses extends AbstractMigration
{
    private $tableName = 'main_metal_expenses';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->tableName)) {
            $table = $this->table($this->tableName);
            $table->addColumn('customer_id', 'biginteger', ['limit' => 20])
                ->addColumn('bank_id', 'biginteger', ['limit' => 20])
                ->addColumn('date', 'date')
                ->addColumn('payment_number', 'integer', ['null' => true])
                ->addColumn('money', 'decimal', ['scale' => 2, 'precision' => 10])
                ->addForeignKey('customer_id', 'customer')
                ->addForeignKey('bank_id', 'bank_account')
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
