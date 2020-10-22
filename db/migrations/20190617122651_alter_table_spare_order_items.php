<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderItems extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_order_items');
        $table->changeColumn('sub_quantity', 'decimal', [
            'scale' => 2, 'precision' => 10,
            'null' => true,
            'default' => null,
        ])->changeColumn('quantity', 'decimal', [
            'scale' => 2, 'precision' => 10,
            'null' => true,
            'default' => null,
        ])->changeColumn('price', 'decimal', [
            'scale' => 4, 'precision' => 10,
            'null' => true,
            'default' => null,
        ])->update();
    }
}
