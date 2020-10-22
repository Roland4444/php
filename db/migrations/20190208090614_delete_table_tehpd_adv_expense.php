<?php

use Phinx\Migration\AbstractMigration;

class DeleteTableTehpdAdvExpense extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'tehpd_adv_expense';

    /**
     * Удаление таблицы
     *
     */
    public function up()
    {
        if ($this->hasTable($this->table)) {
            $this->dropTable($this->table);
        }
    }
}
