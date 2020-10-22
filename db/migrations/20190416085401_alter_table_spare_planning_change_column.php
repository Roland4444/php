<?php

use Phinx\Migration\AbstractMigration;
use Spare\Entity\Planning;

class AlterTableSparePlanningChangeColumn extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'spare_planning';
    protected $column = 'status';

    /**
     * Изменение колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->column, 'enum', [
            'values' => Planning::STATUSES,
            'comment' => 'Статус Планирования',
            'default' => Planning::STATUS_NEW
        ])->update();
    }

    /**
     * Изменение колонок
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        $table->changeColumn($this->column, 'enum', [
            'values' => ['Новая', 'В работе', 'Заказано', 'Закрыто'],
            'comment' => 'Статус заявки',
            'default' => Planning::STATUS_NEW
        ])->update();
    }
}
