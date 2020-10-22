<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareBookingItemsSetDefaultNullOrderItem extends AbstractMigration
{
    protected $table = 'spare_booking_items';
    protected $column = 'order_item_id';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->column, 'integer', [
            'limit' => 11,
            'comment' => 'id пункта заявки',
            'signed' => false,
            'default' => null,
            'null' => true,
        ])->update();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->column, 'integer', [
            'limit' => 11,
            'comment' => 'id пункта заявки',
            'signed' => false,
        ])->update();
    }
}
