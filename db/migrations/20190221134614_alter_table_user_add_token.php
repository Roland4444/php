<?php

use Phinx\Migration\AbstractMigration;

class AlterTableUserAddToken extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user');
        $table->addColumn('token', 'string', ['null' => true])
            ->addColumn('token_expired', 'datetime', ['null' => true])
            ->update();
    }
}
