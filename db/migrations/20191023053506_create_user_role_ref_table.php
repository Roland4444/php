<?php

use Phinx\Migration\AbstractMigration;

class CreateUserRoleRefTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user_role_ref');
        $table->addColumn('user_id', 'biginteger')
            ->addForeignKey('user_id', 'user')
            ->addColumn('role_id', 'biginteger')
            ->addForeignKey('role_id', 'role')
            ->create();

        $this->execute("INSERT INTO user_role_ref (user_id, role_id) SELECT id, role_id FROM user");
    }
}
