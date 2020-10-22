<?php

use Phinx\Migration\AbstractMigration;

class AddRoleWarehouse extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'warehouse']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'warehouse'");
    }
}
