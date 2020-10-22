<?php

use Phinx\Migration\AbstractMigration;

class CreateSpareSellerReturns extends AbstractMigration
{
    protected $table = 'spare_seller_returns';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('date', 'date')
                ->addColumn('seller_id', 'integer')
                ->addForeignKey('seller_id', 'spare_seller')
                ->addColumn('money', 'decimal', ['scale' => 2, 'precision' => 10 ])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->save();
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
