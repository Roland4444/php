<?php

use Phinx\Migration\AbstractMigration;

class AlterCategoryGroupAddOrder extends AbstractMigration
{
    private $tableName = 'cost_category_group';
    private $columnName = 'sortOrder';

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->tableName);
        if (!$table->hasColumn($this->columnName)) {
            $table->addColumn($this->columnName, 'integer')->update();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->tableName);
        if ($table->hasColumn($this->columnName)) {
            $table->removeColumn($this->columnName)->update();
        }
    }
}
