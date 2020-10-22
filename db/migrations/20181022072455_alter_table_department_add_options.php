<?php

use Phinx\Migration\AbstractMigration;

class AlterTableDepartmentAddOptions extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'department';

    protected $column = 'options';

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'json', [
                'default' => null,
                'null' => true,
            ])->update();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }
}
