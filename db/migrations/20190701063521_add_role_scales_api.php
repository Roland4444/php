<?php

use Phinx\Migration\AbstractMigration;

class AddRoleScalesApi extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'scalesapi']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'scalesapi'");
    }
}
