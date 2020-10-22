<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareBookingItems extends AbstractMigration
{
    protected $table = 'spare_booking_items';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('booking_id', 'integer', [
                'limit' => 11,
                'comment' => 'id заказа',
            ])->addColumn('order_item_id', 'integer', [
                'limit' => 11,
                'comment' => 'id пункта заявки',
                'signed' => false,
            ])->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество товара',
            ])->addColumn('sub_quantity', 'integer', [
                'null' => true,
                'limit' => 11,
                'default' => NULL,
                'comment' => 'Количество единиц (штуки, литры итп) в одном пришедшем товаре'
            ])->addColumn('price', 'decimal', [
                'comment' => 'Цена за единицу товара',
                'scale' => 2, 'precision' => 10,
            ])
                ->addIndex('order_item_id')
                ->addIndex('booking_id')
                ->addForeignKey('booking_id', 'spare_booking', ['id'],
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
