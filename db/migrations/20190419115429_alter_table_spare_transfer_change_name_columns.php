<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareTransferChangeNameColumns extends AbstractMigration
{
    protected $table = 'spare_transfer';
    protected $columnOld1 = 'warehouse_exp_id';
    protected $columnOld2 = 'warehouse_imp_id';
    protected $columnNew1 = 'source_id';
    protected $columnNew2 = 'dest_id';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnNew1) && $table->hasColumn($this->columnNew2)) {
            return;
        }
        $table->renameColumn($this->columnOld1, $this->columnNew1)
            ->renameColumn($this->columnOld2, $this->columnNew2)
            ->save();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnOld1) && $table->hasColumn($this->columnOld2)) {
            return;
        }
        $table->renameColumn($this->columnNew1, $this->columnOld1)
            ->renameColumn($this->columnNew2, $this->columnOld2)
            ->save();
    }
}
