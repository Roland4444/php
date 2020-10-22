<?php

use Phinx\Migration\AbstractMigration;

class AlterTableCustomerAddColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'customer';
    protected $column = 'inspection_date';


    /**
     * Добавление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'date', [
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
