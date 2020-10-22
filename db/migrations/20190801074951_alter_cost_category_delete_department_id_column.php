<?php

use Phinx\Migration\AbstractMigration;

class AlterCostCategoryDeleteDepartmentIdColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('cost_category');

        if ($table->hasColumn('department_id')) {
            $table
                ->dropForeignKey('department_id')
                ->removeColumn('department_id')
                ->update();
        }
    }
}
