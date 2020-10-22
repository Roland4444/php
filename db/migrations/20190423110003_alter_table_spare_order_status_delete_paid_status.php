<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareOrderStatusDeletePaidStatus extends AbstractMigration
{
    protected $table = 'spare_order_status';

    protected $data = [
        [
            'alias' => 'PAID',
            'title' => 'Оплачен',
        ]
    ];

    /**
     * Создание таблицы
     */
    public function up()
    {
        $sql = "DELETE FROM {$this->table} WHERE alias = 'PAID'";
        $this->execute($sql);
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        $sql = "SELECT id FROM {$this->table} WHERE alias = 'PAID'";
        $result = $this->fetchAll($sql);
        if (! empty($result)) {
            return;
        }
        $table = $this->table($this->table);
        $table->insert($this->data)->save();
    }
}
