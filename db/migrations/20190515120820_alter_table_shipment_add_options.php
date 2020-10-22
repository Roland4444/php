<?php

use Phinx\Migration\AbstractMigration;

class AlterTableShipmentAddOptions extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('shipment');
        $table->addColumn('options', 'json', [
            'default' => null,
            'null' => true,
        ])->save();
    }
}
