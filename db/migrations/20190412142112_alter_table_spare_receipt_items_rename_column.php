<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareReceiptItemsRenameColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'spare_receipt_items';
    protected $columnNameOld = 'booking_item_id';
    protected $columnNameNew = 'order_item_id';

    /**
     * Изменение имени таблицы
     */
    public function up()
    {
        $table = $this->table($this->table);

        if ($table->hasColumn($this->columnNameOld)) {
            $table->renameColumn($this->columnNameOld, $this->columnNameNew)->save();
        }
    }

    /**
     * Изменение имени таблицы
     */
    public function down()
    {
        $table = $this->table($this->table);

        if ($table->hasColumn($this->columnNameNew)) {
            $table->renameColumn($this->columnNameNew, $this->columnNameOld)->save();
        }
    }
}
