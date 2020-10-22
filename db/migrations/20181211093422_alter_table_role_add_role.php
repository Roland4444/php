<?php

use Phinx\Migration\AbstractMigration;

class AlterTableRoleAddRole extends AbstractMigration
{
    const ROLE = 'waybill';
    protected $table = 'role';

    public function change()
    {
        $data = [
            [
                'name' => self::ROLE,
            ]
        ];
        $table = $this->table($this->table);
        $table->insert($data)->save();
    }
}
