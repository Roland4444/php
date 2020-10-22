<?php

use Phinx\Migration\AbstractMigration;
use Reference\Entity\Settings;

class CreateTableSettings extends AbstractMigration
{
    protected $table = 'settings';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('label', 'string', [
                'limit' => 250,
                'comment' => 'Описание поля',
            ])->addColumn('alias', 'string', [
                'limit' => 250,
                'comment' => 'Алиас',
            ])->addColumn('value', 'string', [
                'limit' => 250,
                'comment' => 'Значение.',
            ])
                ->save();

            $date = [
                [
                    'label' => 'Алмаз комиссия',
                    'alias' => Settings::DIAMOND_COMMISSION,
                    'value' => '1.5',
                ],
                [
                    'label' => 'Алмаз счет',
                    'alias' => 'diamond_bank_account',
                    'value' => 'rnko',
                ],
            ];
            $table->insert($date)->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->table)) {
            $this->dropTable($this->table);
        }
    }
}
