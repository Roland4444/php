<?php

use Phinx\Migration\AbstractMigration;

class CreateSpareConsumptionTableAndPopulateWithData extends AbstractMigration
{
    public function change()
    {
        // Создать таблицу spare_consumption
        $spareConsumption = $this->table('spare_consumption');
        $spareConsumption
        ->addColumn('date', 'date')
        ->addColumn('employee_id', 'integer')
        ->addForeignKey('employee_id', 'employees', 'id')
        ->addColumn('warehouse_id', 'integer')
        ->addForeignKey('warehouse_id', 'warehouse', 'id')
        ->create();
        
        // Перенести данные из spare_consumption_items в новую таблицу spare_consumption
        $this->execute("INSERT INTO spare_consumption (id, date, employee_id, warehouse_id) SELECT id, date, employee_id, warehouse_id FROM spare_consumption_items");
        
        // Добавить column для spare_consumption в таблицу spare_consumption_items
        $spareConsumptionItems = $this->table('spare_consumption_items');
        $spareConsumptionItems
        ->addColumn('consumption_id', 'integer',  ['default' => 1, ])
        ->save();
        
        // Заполнить ссылки на spare_consumption в spare_consumption_items
        $this->execute("update spare_consumption_items, spare_consumption set spare_consumption_items.consumption_id = spare_consumption.id where spare_consumption.id = spare_consumption_items.id;");
        
        // Добавить foreign key для spare_consumption в таблицу spare_consumption_items
        $spareConsumptionItems = $this->table('spare_consumption_items');
        $spareConsumptionItems
        ->addForeignKey('consumption_id', 'spare_consumption', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
        ->save();
        
    }
}
