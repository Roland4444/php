<?php

use Phinx\Migration\AbstractMigration;

class AlterTableWaybillAddColumnFuelConsumption extends AbstractMigration
{
    protected $table = 'waybill';
    protected $column = 'fuel_consumption';

    /**
     * Добавление колонки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->column)) {
            return;
        }

        $table->addColumn($this->column, 'string', [
            'limit' => 10,
            'default' => null,
            'null' => true,
        ])->update();

        $this->setDataInTable();
    }

    /**
     * Удаление колонки
     */
    public function down()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table->removeColumn($this->column)->save();
    }

    /**
     * Добавляет данные из таблицы техники в таблицу путевых листов
     */
    private function setDataInTable()
    {

        $sql = "SELECT id, {$this->column} FROM vehicle WHERE {$this->column} IS NOT NULL";
        $data = $this->fetchAll($sql);

        if (empty($data)) {
            return;
        }

        $sqlUpdate = '';

        foreach ($data as $vehicleData) {
            $sqlUpdate .= "UPDATE waybill SET 
                              {$this->column} = '{$vehicleData[$this->column]}'  
                              WHERE vehicle_id = '{$vehicleData['id']}'; ";
        }

        $this->execute($sqlUpdate);
    }
}
