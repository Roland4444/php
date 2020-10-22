<?php

use Phinx\Migration\AbstractMigration;

class AlterPurchaseTableSetCostColumnNotNull extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('purchase');
        $table->changeColumn('cost', 'decimal', [
            'scale' => 3,
            'precision' => 10,
            'null' => false,
        ])->save();
    }
}
