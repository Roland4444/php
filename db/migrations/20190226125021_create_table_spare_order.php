<?php

use Phinx\Migration\AbstractMigration;
use Spare\Entity\Planning;

class CreateTableSpareOrder extends AbstractMigration
{
    protected $table = 'spare_orders';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('date_create', 'date')
                ->addColumn('storage_comment', 'string', [
                    'limit' => 250,
                    'comment' => 'Комментарий кладовщика',
                ])->addColumn('supplier_comment', 'string', [
                    'limit' => 250,
                    'comment' => 'Комментарий снабженца',
                ])->addColumn('status', 'enum', [
                    'values' => Planning::STATUSES,
                    'comment' => 'Статус заявки',
                    'default' => 'Новая'
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
