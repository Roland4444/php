<?php

use Phinx\Migration\AbstractMigration;

class UpdateUserTableDeleteRoleIdColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user');

        if ($table->hasColumn('role_id')) {
            $table->dropForeignKey('role_id')->save();
            $table->removeColumn('role_id')->save();
        }
    }
}
