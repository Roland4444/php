<?php

use Phinx\Migration\AbstractMigration;

class RenameTableSpareInvoiceItems extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $tableNameOld = 'spare_invoice_items';
    protected $tableNameNew = 'spare_receipt_items';

    protected $oldNameColumn = 'invoice_id';
    protected $newNameColumn = 'receipt_id';

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
        if ($table->hasColumn($this->oldNameColumn)) {
            $table->renameColumn($this->oldNameColumn, $this->newNameColumn);
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

        if ($table->hasColumn($this->newNameColumn)) {
            $table->renameColumn($this->newNameColumn, $this->oldNameColumn);
        }
    }
}
