<?php

use Phinx\Migration\AbstractMigration;

class DeleteEmployeeIdDateColumnsFromSpareCosnumptionItemsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_consumption_items');
        
        if ($table->hasColumn('date')) {
            $table->removeColumn('date')
            ->update();
        }
        
        if ($table->hasColumn('employee_id')) {
            $table->dropForeignKey('employee_id')->save();
            $table->removeColumn('employee_id')
            ->save();
        }
        
        if ($table->hasColumn('warehouse_id')) {
            $table->dropForeignKey('warehouse_id')->save();
            $table->removeColumn('warehouse_id')
            ->save();
        }
    }
}
