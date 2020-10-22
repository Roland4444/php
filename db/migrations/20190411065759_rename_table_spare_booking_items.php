<?php

use Phinx\Migration\AbstractMigration;

class RenameTableSpareBookingItems extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $tableNameOld = 'spare_booking_items';
    protected $tableNameNew = 'spare_order_item';

    /**
     * Изменение имени таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->tableNameOld)) {
            return;
        }

        $table = $this->table($this->tableNameOld);
        $table->rename($this->tableNameNew)->update();

        if ($table->hasColumn('booking_id')) {
            $table->renameColumn('booking_id', 'order_id')->save();
        }

        if ($table->hasColumn('order_item_id')) {
            $table->renameColumn('order_item_id', 'planning_items_id')->save();
        }
    }

    /**
     * Изменение имени таблицы
     */
    public function down()
    {
        if (! $this->hasTable($this->tableNameNew)) {
            return;
        }
        $table = $this->table($this->tableNameNew);
        $table->rename($this->tableNameOld)->update();

        if ($table->hasColumn('order_id')) {
            $table->renameColumn('order_id', 'booking_id');
        }

        if ($table->hasColumn('planning_items_id')) {
            $table->renameColumn('planning_items_id', 'order_item_id');
        }
    }
}
