<?php

use Phinx\Migration\AbstractMigration;

class AlterTableStorageMetalExpenseAddWeighingIdColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('storage_metal_expense');
    
        $table
            ->addColumn('weighing_id', 'integer', ['null' => true])
            ->addForeignKey('weighing_id', 'weighings', 'id',
                ['delete' => 'SET_NULL', 'update' => 'SET_NULL'])
            ->save();
    }
}
