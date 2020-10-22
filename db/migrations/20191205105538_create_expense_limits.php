<?php

use Phinx\Migration\AbstractMigration;

class CreateExpenseLimits extends AbstractMigration
{
    protected $table = 'report_expense_limits';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $data = [['id' => '1', 'data' => '[]']];
            $table->addColumn('data', 'json')
                ->insert($data)
                ->save();

            $table->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->table)) {
            $table = $this->table($this->table);
            $table->drop()->save();
        }
    }
}
