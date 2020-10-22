<?php

use Phinx\Migration\AbstractMigration;

class AddRoleOfficeCashBankView extends AbstractMigration
{
    public function up() {
        $table = $this->table('role');
        $data = [['name' => 'OfficeCashBankView']];
        $table->insert($data)->save();
    }

    public function down()
    {
        $this->execute("DELETE FROM role WHERE `name` = 'OfficeCashBankView'");
    }
}
