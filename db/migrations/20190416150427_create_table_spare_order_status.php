<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareOrderStatus extends AbstractMigration
{
    protected $table = 'spare_order_status';

    protected $data = [
        [
            'alias' => 'NEW',
            'title' => 'Новый',
        ],
        [
            'alias' => 'IN_WORK',
            'title' => 'В работе',
        ],
        [
            'alias' => 'PAID',
            'title' => 'Оплачен',
        ],
        [
            'alias' => 'CLOSED',
            'title' => 'Закрыт',
        ]
    ];

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('title', 'string', [
                'comment' => 'Название статуса',
                'limit' => 25
            ])->addColumn('alias', 'string', [
                'comment' => 'Алиас',
                'limit' => 25
            ])->save();

            $table->insert($this->data)->save();
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
