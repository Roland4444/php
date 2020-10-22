<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWaybillAddColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'waybill';
    protected $column = 'waybill_number';

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'integer', [
                'limit' => 15,
                'comment' => 'Номер накладной',
            ])->update();
        }
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
