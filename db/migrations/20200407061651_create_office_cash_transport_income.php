<?php

use Phinx\Migration\AbstractMigration;

class CreateOfficeCashTransportIncome extends AbstractMigration
{
    protected string $table = 'office_cash_transport_income';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $this->table($this->table)
                ->addColumn('date', 'date')
                ->addColumn('money', 'decimal', ['scale' => 2, 'precision' => 10])
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->table)) {
            $this->table($this->table)->drop()->save();
        }
    }
}
