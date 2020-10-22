<?php

use Phinx\Migration\AbstractMigration;

class AddRoleSpareView extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'SpareView']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'SpareView'");
    }
}
