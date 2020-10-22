<?php

use Phinx\Migration\AbstractMigration;

class AlterTableFactoringPaymentsAddBankId extends AbstractMigration
{
    protected $table = 'factoring_payments';
    protected $column = 'bank_id';
    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'biginteger', ['limit' => 20])
                ->addForeignKey($this->column, 'bank_account')
                ->save();
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
