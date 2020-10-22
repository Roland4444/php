<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareBookingItemsAddColumnSpare extends AbstractMigration
{
    protected $table = 'spare_booking_items';
    protected $column = 'spare_id';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            return;
        }

        $table->addColumn($this->column, 'biginteger', [
            'limit' => 20,
            'comment' => 'id запчасти',
            'signed' => false,
        ])
            ->addForeignKey($this->column, 'spares', ['id'])
            ->save();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table->dropForeignKey($this->column)
            ->removeIndexByName($this->column)
            ->removeColumn($this->column)
            ->save();
    }
}
