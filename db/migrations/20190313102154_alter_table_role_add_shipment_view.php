<?php

use Phinx\Migration\AbstractMigration;

class AlterTableRoleAddShipmentView extends AbstractMigration
{
    const ROLE = 'shipmentView';
    protected $table = 'role';

    /**
     * Добавление роли
     */
    public function up()
    {
        $sql = "SELECT * FROM {$this->table}  WHERE name = '". self::ROLE . "'";
        if (! empty($this->fetchAll($sql))){
            return;
        }
        $data = [
            [
                'name' => self::ROLE,
            ]
        ];
        $table = $this->table($this->table);
        $table->insert($data)->save();
    }

    /**
     * Удаление роли
     */
    public function down()
    {
        $sql = "DELETE FROM {$this->table} WHERE name = '". self::ROLE . "'";
        $this->execute($sql);
    }
}
