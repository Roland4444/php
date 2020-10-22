<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareBooking extends AbstractMigration
{
    protected $table = 'spare_booking';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('document', 'string',
                [
                    'limit' => 200,
                    'comment' => 'Индефикатор документа заказа'
                ])
                ->addColumn('seller_id', 'integer',
                    [
                        'limit' => 11,
                        'comment' => 'Поставщик'
                    ])
                ->addColumn('date', 'date',
                    [
                        'comment' => 'Дата заказа'
                    ])
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
