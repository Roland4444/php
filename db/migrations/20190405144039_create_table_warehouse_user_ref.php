<?php

use Phinx\Migration\AbstractMigration;

class CreateTableWarehouseUserRef extends AbstractMigration
{
    protected $table = 'warehouse_user_ref';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('user_id', 'biginteger', ['limit' => 20])
                ->addColumn('warehouse_id', 'integer',[
                    'limit' => 11
                ])
                ->addForeignKey('user_id', 'user')
                ->addForeignKey('warehouse_id', 'warehouse', ['id'],
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
