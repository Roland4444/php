<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderAddPaymentStatus extends AbstractMigration
{
    protected $table = 'spare_order';
    protected $column = 'expense_id';
    protected $newColumn = 'payment_status_id';

    /**
     * Удаление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table
            ->dropForeignKey($this->column)
            ->removeColumn($this->column)
            ->addColumn($this->newColumn, 'integer', [
                'limit' => 11,
                'default' => null,
                'null' => true,
            ])
            ->addForeignKey($this->newColumn, 'spare_order_status_payment')
            ->save();
    }

    /**
     * Добавление колонки
     */
    public function down()
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
            ->dropForeignKey($this->newColumn)
            ->removeColumn($this->newColumn)
            ->save();
    }
}
