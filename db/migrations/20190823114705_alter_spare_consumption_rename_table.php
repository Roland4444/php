<?php

use Phinx\Migration\AbstractMigration;

class AlterSpareConsumptionRenameTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('spare_consumption');
        $table->rename('spare_consumption_items');
    }
    
    public function down()
    {
        $table = $this->table('spare_consumption_items');
        $table->rename('spare_consumption');
    }
}
