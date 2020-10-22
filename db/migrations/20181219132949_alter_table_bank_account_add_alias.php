<?php

use Phinx\Migration\AbstractMigration;

class AlterTableBankAccountAddAlias extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'bank_account';
    protected $column = 'alias';

    /**
     * Добавление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'string', [
                'limit' => 250,
                'default' => null,
                'null' => true,
            ])->update();
        }
    }

    /**
     * Удаление колоноки
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }
}
