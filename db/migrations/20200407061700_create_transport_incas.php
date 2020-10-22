<?php

use Phinx\Migration\AbstractMigration;

class CreateTransportIncas extends AbstractMigration
{
    protected string $table = 'transport_incas';

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
