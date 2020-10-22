<?php

use Phinx\Migration\AbstractMigration;

class AlterTableMetalDeleteType extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'metal';
    protected $column = 'type';

    /**
     * Удаление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }
}
