<?php

use Phinx\Migration\AbstractMigration;

class DeleteSettingRnko extends AbstractMigration
{
    protected $table = 'settings';

    /**
     * Удаление данных
     */
    public function up()
    {
        $sql = "DELETE FROM {$this->table} WHERE alias = 'diamond_bank_account'";
        $this->execute($sql);
    }

    /**
     * Добавление данных
     */
    public function down()
    {
        $sql = "SELECT * FROM {$this->table} WHERE alias = 'diamond_bank_account'";
        if (! empty($this->query($sql)->fetchAll())){
            return;
        }

        $table = $this->table($this->table);
        $date = [
            [
                'label' => 'Алмаз счет',
                'alias' => 'diamond_bank_account',
                'value' => 'rnko',
            ],
        ];
        $table->insert($date)->save();
    }
}
