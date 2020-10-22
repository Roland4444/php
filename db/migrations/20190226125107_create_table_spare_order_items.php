<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareOrderItems extends AbstractMigration
{
    protected $table = 'spare_order_items';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('order_id', 'integer', [
                'limit' => 11,
                'comment' => 'id заказа',
            ])->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество едениц в позиции заказа',
            ])
                ->addIndex('spare_id')
                ->addIndex('order_id')
                ->addForeignKey('spare_id', 'spares', ['id'])
                ->addForeignKey('order_id', 'spare_orders', ['id'],
                    ['delete' => 'CASCADE', 'update' => 'CASCADE'])
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
