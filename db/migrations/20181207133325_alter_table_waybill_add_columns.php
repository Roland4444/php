<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWaybillAddColumns extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'waybill';
    protected $columnMechanic = \Modules\Entity\WaybillSettings::MECHANIC;
    protected $columnDispatcher = \Modules\Entity\WaybillSettings::DISPATCHER;

    /**
     * Добавление колонки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->columnMechanic)) {
            $table->addColumn($this->columnMechanic, 'string', [
                'comment' => 'ФИО механика',
                'limit' => 20,

            ])
            ->addColumn($this->columnDispatcher, 'string', [
                'comment' => 'ФИО диспетчера',
                'limit' => 20,

            ])
            ->update();
        }

        $sql = $this->getSql($this->getData());

        if (!empty($sql)) {
            $this->query($sql);
        }

    }

    /**
     * Удаление колонок
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnMechanic)) {
            $table->removeColumn($this->columnMechanic)
                ->removeColumn($this->columnDispatcher)
                ->update();
        }
    }

    /**
     * Возвращает данные дефолтных значений для создания путевого листа
     *
     * @return int
     */
    protected function getData()
    {
        $sql = "SELECT id, name, value FROM waybill_settings";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Возвращает sql запрос для обнавления данных
     *
     * @param $date
     * @return string
     */
    protected function getSql($date)
    {
        $sql = '';

        if (empty($date)) {
            return $sql;
        }

        $dispatcher = '';
        $mechanic = '';
        foreach ($date as $row) {
            if ($row['name'] == $this->columnDispatcher) {
                $dispatcher = $row['value'];
            }
            if ($row['name'] == $this->columnMechanic) {
                $mechanic = $row['value'];
            }
        }

        $sql = "UPDATE {$this->table} 
                    SET {$this->columnDispatcher} = '$dispatcher' 
                        WHERE  {$this->columnDispatcher} = '';
                UPDATE {$this->table} 
                    SET {$this->columnMechanic} = '$mechanic' 
                        WHERE  {$this->columnMechanic} = ''; ";
        return $sql;
    }
}
