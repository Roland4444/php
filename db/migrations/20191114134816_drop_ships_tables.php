<?php

use Phinx\Migration\AbstractMigration;

class DropShipsTables extends AbstractMigration
{
    public function change()
    {
        $this->dropTable('ships_bonus');
        $this->dropTable('ships_category');
        $this->dropTable('ships_product');
        $this->dropTable('ships_settings');
    }
}
