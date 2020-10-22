<?php

use Phinx\Migration\AbstractMigration;

class AlterTableCustomerDeleteColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'customer';
    protected $column = 'inn';

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
