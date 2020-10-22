<?php

use Phinx\Migration\AbstractMigration;

class AddRoleSupply extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'supply']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'supply'");
    }
}
