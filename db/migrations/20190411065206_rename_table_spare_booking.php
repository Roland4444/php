<?php

use Phinx\Migration\AbstractMigration;

class RenameTableSpareBooking extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $tableNameOld = 'spare_booking';
    protected $tableNameNew = 'spare_order';
    protected $column = 'warehouse_id';

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

        if ($table->hasColumn('warehouse_id')) {
            $table->dropForeignKey($this->column)
                ->removeIndexByName($this->column)
                ->removeColumn($this->column)
                ->save();
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

        if (! $table->hasColumn('warehouse_id')) {
            $table->addColumn($this->column, 'integer', ['limit' => 11])
                ->addForeignKey($this->column, 'warehouse')
                ->save();
        }
    }
}
