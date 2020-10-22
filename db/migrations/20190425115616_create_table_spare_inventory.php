<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareInventory extends AbstractMigration
{
    protected $table = 'spare_inventory';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('date', 'date', [
                'comment' => 'Дата проведения инвентаризации',
            ])
                ->addColumn('warehouse_id', 'integer', [
                'limit' => 11,
                'comment' => 'id склада'
            ])
                ->addForeignKey('warehouse_id', 'warehouse')
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
