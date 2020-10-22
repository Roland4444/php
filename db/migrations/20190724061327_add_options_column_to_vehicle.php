<?php

use Phinx\Migration\AbstractMigration;
use Reference\Entity\Vehicle;

class AddOptionsColumnToVehicle extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('vehicle');
        $table->addColumn('options', 'json', [
            'default' => null,
            'null' => true,
            'comment' => 'Опции транспорта'
        ])
            ->update();
    }
}
