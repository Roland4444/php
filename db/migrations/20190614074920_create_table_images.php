<?php

use Phinx\Migration\AbstractMigration;

class CreateTableImages extends AbstractMigration
{
    private $table = 'images';

    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('entity', 'string')
                ->addColumn('entity_id', 'integer')
                ->addColumn('filename', 'string')
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
