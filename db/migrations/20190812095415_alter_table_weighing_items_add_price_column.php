<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWeighingItemsAddPriceColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('weighing_items');
    
        $table
            ->addColumn('price', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => true
            ])
            ->save();
    }
}
