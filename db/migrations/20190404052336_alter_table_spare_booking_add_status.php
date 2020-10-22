<?php
use Phinx\Migration\AbstractMigration;
use \Spare\Entity\Order;

class AlterTableSpareBookingAddStatus extends AbstractMigration
{
    protected $table = 'spare_booking';
    protected $columnsStatus = 'status';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->columnsStatus)) {
            $table->addColumn($this->columnsStatus, 'enum',[
                'values' => Order::STATUSES,
                'comment' => 'Статус заказа',
                'default' => Order::STATUS_NEW
            ])->update();
        }
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnsStatus)) {
            $table->removeColumn($this->columnsStatus)->update();
        }
    }
}
