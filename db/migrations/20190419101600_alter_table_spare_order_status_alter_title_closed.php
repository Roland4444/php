<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderStatusAlterTitleClosed extends AbstractMigration
{
    protected $table = 'spare_order_status';

    /**
     * Установка нового значения
     */
    public function up()
    {
        $sql = "UPDATE {$this->table} SET title='Поступил на склад' WHERE alias = 'CLOSED'";
        $this->execute($sql);
    }

    /**
     * Установка старого значения
     */
    public function down()
    {
        $sql = "UPDATE {$this->table} SET title='Закрыт' WHERE alias = 'CLOSED'";
        $this->execute($sql);
    }
}
