<?php

use Phinx\Migration\AbstractMigration;

class AlterSpareConsumptionQuantityToDecimal extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spare_consumption');
        $table->changeColumn('quantity', 'decimal', ['scale' => 2, 'precision' => 10])
            ->update();
    }
}
