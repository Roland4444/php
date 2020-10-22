<?php

use Phinx\Migration\AbstractMigration;

class RenameTableSpareOrderItem extends AbstractMigration
{
    protected $tableOld = 'spare_order_item';
    protected $tableNew = 'spare_order_items';

    public function up()
    {
        if ($this->hasTable($this->tableNew)) {
            return;
        }
        $table = $this->table($this->tableOld);
        $table->rename($this->tableNew)->save();
    }

    public function down()
    {
        if ($this->hasTable($this->tableOld)) {
            return;
        }
        $table = $this->table($this->tableNew);
        $table->rename($this->tableOld)->save();
    }
}
