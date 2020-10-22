<?php

use Phinx\Migration\AbstractMigration;

class DeleteMovableColumnFromVehicleTable extends AbstractMigration
{
    protected $column = 'movable';


    public function change()
    {
        // Перенос данных из колонки movable в колонку options
        $this->execute("update vehicle set options = '[\"movable\"]' where movable = 1"); // returns the number of affected rows

        $table = $this->table('vehicle');

        // Удаление колонки movable
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)
                ->update();
        }

    }
}
