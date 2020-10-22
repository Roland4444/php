<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareInvoiceItems extends AbstractMigration
{
    protected $table = 'spare_invoice_items';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('invoice_id', 'integer', [
                'limit' => 11,
                'comment' => 'id прихода',
            ])->addColumn('booking_item_id', 'integer', [
                'limit' => 11,
                'comment' => 'id пункта заказа',
                'signed' => false,
            ])->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество пришедшего товара',
            ])->addColumn('sub_quantity', 'integer', [
                'null' => true,
                'limit' => 11,
                'default' => NULL,
                'comment' => 'Количество единиц (штуки, литры итп) в одном пришедшем товаре'
            ])->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])
                ->addIndex('booking_item_id')
                ->addIndex('invoice_id')
                ->addIndex('spare_id')
                ->addForeignKey('spare_id', 'spares', ['id'])
                ->addForeignKey('invoice_id', 'spare_invoice', ['id'],
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
