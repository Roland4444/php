<?php

use Phinx\Migration\AbstractMigration;

class AlterTableCostCategoryDeleteModule extends AbstractMigration
{
    protected $table = 'cost_category';
    protected $column = 'module';

    /**
     * Удаление колонки
     *
     */
    public function change()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }
}
