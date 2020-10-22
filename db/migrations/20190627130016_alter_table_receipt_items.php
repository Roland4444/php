<?php

use Phinx\Migration\AbstractMigration;

class AlterTableReceiptItems extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_receipt_items');
        $table->changeColumn('sub_quantity', 'decimal', [
            'scale' => 2, 'precision' => 10,
            'null' => true,
            'default' => null,
        ])->changeColumn('quantity', 'decimal', [
            'scale' => 2, 'precision' => 10,
            'default' => null,
        ])->update();
    }
}
