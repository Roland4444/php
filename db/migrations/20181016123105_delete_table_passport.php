<?php

use Phinx\Migration\AbstractMigration;

class DeleteTablePassport extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'passport';

    /**
     * Удаление таблицы
     *
     */
    public function up()
    {

        if (!empty($this->query("SHOW TABLES LIKE '" . $this->table . "'")->fetchAll())) {
            $this->dropTable($this->table);
        }

    }
}
