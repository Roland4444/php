<?php

use Phinx\Migration\AbstractMigration;

class AlterTableMainReceiptsAddOptions extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'main_receipts';
    protected $column = 'options';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            return;
        }

        $table->addColumn($this->column, 'json', [
            'default' => null,
            'null' => true,
        ])->update();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }

}
