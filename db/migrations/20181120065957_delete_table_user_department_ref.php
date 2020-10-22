<?php

use Phinx\Migration\AbstractMigration;

class DeleteTableUserDepartmentRef extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'user_department_ref';

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
