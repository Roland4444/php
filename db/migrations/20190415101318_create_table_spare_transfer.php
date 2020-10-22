<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareTransfer extends AbstractMigration
{
    protected $table = 'spare_transfer';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('warehouse_exp_id', 'integer', [
                'limit' => 20,
                'comment' => 'Склад, откуда забираем позицию'
            ])
                ->addColumn('warehouse_imp_id', 'integer', [
                'limit' => 20,
                'comment' => 'Склад, куда передаем позицию'
            ])
                ->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])
                ->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество товара',
            ])
                ->addColumn('date', 'date', [
                'comment' => 'Дата заказа'
            ])
                ->addForeignKey('warehouse_exp_id', 'warehouse')
                ->addForeignKey('warehouse_imp_id', 'warehouse')
                ->addForeignKey('spare_id', 'spares', ['id'])
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
