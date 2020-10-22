<?php

use Phinx\Migration\AbstractMigration;

class AddRoleStorekeeper extends AbstractMigration
{
    const ROLE_NAME = 'storekeeper';

    /**
     * Добавление данных
     */
    public function up()
    {
        $sql = "SELECT * FROM role WHERE name = '" . self::ROLE_NAME . "'";
        if (! empty($this->fetchAll($sql))) {
            return;
        }
        $tableRole = $this->table('role');
        $date = [
            ['name' => self::ROLE_NAME],
        ];
        $tableRole->insert($date)->save();
    }

    /**
     * Удаление данных
     */
    public function down()
    {
        $sql = "DELETE FROM role WHERE name = '" . self::ROLE_NAME . "'";
        $this->execute($sql);
    }
}
