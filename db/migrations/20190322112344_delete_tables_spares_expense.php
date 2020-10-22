<?php

use Phinx\Migration\AbstractMigration;

class DeleteTablesSparesExpense extends AbstractMigration
{
    protected $table = 'spares_expense';

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
