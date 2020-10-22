<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareConsumption extends AbstractMigration
{
    protected $table = 'spare_consumption';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->addColumn('employee_id', 'integer', [
                'limit' => 11,
                'comment' => 'id сотрудника',
            ])->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество едениц в расходе',
            ])->addColumn('date', 'date', [
                'comment' => 'Дата расхода'
            ])->addColumn('comment', 'string', [
                'limit' => 250,
                'comment' => 'Комментарий',
            ])
                ->addIndex('spare_id')
                ->addIndex('employee_id')
                ->addIndex('quantity')
                ->addForeignKey('spare_id', 'spares', ['id'])
                ->addForeignKey('employee_id', 'employees', ['id'])
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
