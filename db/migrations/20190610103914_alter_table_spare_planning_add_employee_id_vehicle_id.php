<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSparePlanningAddEmployeeIdVehicleId extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_planning');
        $table->addColumn('employee_id', 'integer', ['null' => true])
            ->addColumn('vehicle_id', 'biginteger', ['null' => true, 'limit' => 20])
            ->addForeignKey('employee_id', 'employees')
            ->addForeignKey('vehicle_id', 'vehicle')
            ->update();
    }
}
