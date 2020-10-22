<?php

use Phinx\Migration\AbstractMigration;

class AddRoleNonFerrousTransfer extends AbstractMigration
{
    public function change()
    {
        $data = [
            [
                'name' => 'NonFerrousTransfer',
            ]
        ];
        $table = $this->table('role');
        $table->insert($data)
            ->save();

    }
}
