<?php

use Phinx\Migration\AbstractMigration;

class DeleteTablesSparesIncoming extends AbstractMigration
{
    protected $table = 'spares_incoming';

    /**
     * Удаление таблицы
     */
    public function up()
    {
        if ($this->hasTable($this->table)) {
            $this->dropTable($this->table);
        }
    }
}
