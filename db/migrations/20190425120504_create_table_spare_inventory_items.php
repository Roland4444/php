<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareInventoryItems extends AbstractMigration
{
    protected $table = 'spare_inventory_items';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('inventory_id', 'integer', [
                'limit' => 11,
                'comment' => 'id инвентаризации',
            ])->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])->addColumn('quantity_formal', 'integer', [
                'limit' => 11,
                'comment' => 'Количество едениц по базе',
            ])->addColumn('quantity_fact', 'integer', [
                'limit' => 11,
                'comment' => 'Количество реальное',
            ])->addForeignKey('spare_id', 'spares', ['id'])
                ->addForeignKey('inventory_id', 'spare_inventory', ['id'],
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
