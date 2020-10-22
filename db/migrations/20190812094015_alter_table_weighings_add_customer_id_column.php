<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWeighingsAddCustomerIdColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('weighings');
    
        $table
            ->addColumn('customer_id', 'biginteger', ['null' => true])
            ->addForeignKey('customer_id', 'customer', 'id',
                ['delete' => 'SET_NULL', 'update' => 'SET_NULL'])
            ->save();
    }
}
