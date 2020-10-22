<?php

use Phinx\Migration\AbstractMigration;

class AlterSpareInventoryItemsTableQuantityColumnsToDecimal extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_inventory_items');
        $table->changeColumn('quantity_formal', 'decimal', ['scale' => 2, 'precision' => 10])
            ->changeColumn('quantity_fact', 'decimal', ['scale' => 2, 'precision' => 10])
            ->update();
    }
}
