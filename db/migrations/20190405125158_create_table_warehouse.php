<?php

use Phinx\Migration\AbstractMigration;

class CreateTableWarehouse extends AbstractMigration
{
    protected $table = 'warehouse';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('name', 'string',
                [
                    'limit' => 200,
                    'comment' => 'Название склада'
                ])
                ->addColumn('options', 'json', [
                    'default' => null,
                    'null' => true,
                ])
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
