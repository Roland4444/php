<?php

use Phinx\Migration\AbstractMigration;

class DeleteTablesSparesDocuments extends AbstractMigration
{
    protected $table = 'spares_documents';

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
