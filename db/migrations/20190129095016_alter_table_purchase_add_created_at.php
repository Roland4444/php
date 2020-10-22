<?php

use Phinx\Migration\AbstractMigration;

class AlterTablePurchaseAddCreatedAt extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'purchase';
    protected $column = 'created_at';


    /**
     * Добавление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])->update();

            $sql = "UPDATE {$this->table} SET {$this->column} = '2000-01-01 00:00:00'";
            $this->query($sql);
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
