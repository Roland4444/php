<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSparePlanningItems extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_planning_items');
        $table->changeColumn('quantity', 'decimal', [
            'scale' => 2, 'precision' => 10,
            'null' => true,
            'default' => null,
        ])->update();
    }
}
