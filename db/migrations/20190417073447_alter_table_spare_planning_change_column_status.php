<?php

use Phinx\Migration\AbstractMigration;
use \Spare\Entity\Planning;

class AlterTableSparePlanningChangeColumnStatus extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'spare_planning';
    protected $column = 'status';
    protected $columnNew = 'status_id';

    /**
     * Изменение колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnNew)) {
            return;
        }

        $table->removeColumn($this->column)
            ->addColumn($this->columnNew, 'integer', ['limit' => 11])
            ->addForeignKey($this->columnNew, 'spare_planning_status')
            ->save();
    }

    /**
     * Изменение колонок
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        $table->removeColumn($this->columnNew)
            ->addColumn($this->column, 'enum', [
            'values' => Planning::STATUSES,
            'comment' => 'Статус Планирования',
            'default' => Planning::STATUS_NEW
        ])->update();

    }
}
