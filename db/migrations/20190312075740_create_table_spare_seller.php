<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareSeller extends AbstractMigration
{
    protected $table = 'spare_seller';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('name', 'string', ['limit' => 200])
                ->addColumn('inn', 'string', ['limit' => 20])
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
