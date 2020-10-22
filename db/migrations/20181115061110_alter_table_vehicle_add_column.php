<?php

use Phinx\Migration\AbstractMigration;

class AlterTableVehicleAddColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'vehicle';
    protected $column = 'fuel_consumption';


    /**
     * Добавление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'string', [
                'limit' => 10,
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
