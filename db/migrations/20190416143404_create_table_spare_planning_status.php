<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSparePlanningStatus extends AbstractMigration
{
    protected $table = 'spare_planning_status';
    protected $data = [
        [
            'alias' => 'NEW',
            'title' => 'Новая',
        ],
        [
            'alias' => 'IN_WORK',
            'title' => 'В работе',
        ],
        [
            'alias' => 'ORDERED',
            'title' => 'Заказано',
        ],
        [
            'alias' => 'CLOSED',
            'title' => 'Закрыто',
        ]
    ];

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->table)) {
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
