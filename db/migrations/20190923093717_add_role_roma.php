<?php

use Phinx\Migration\AbstractMigration;

class AddRoleRoma extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'roma']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'roma'");
    }
}
