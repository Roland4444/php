<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSparePaymentStatus extends AbstractMigration
{
    protected $table = 'spare_order_status_payment';
    protected $data = [
        [
            'alias' => 'NOT_PAYMENT',
            'title' => 'Не оплачен',
        ],
        [
            'alias' => 'PARTIALLY_PAID',
            'title' => 'Частично оплачен',
        ],
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
