<?php

use Phinx\Migration\AbstractMigration;

class UpdateMoveOfVehicle extends AbstractMigration
{
    public function up()
    {
        $sql = "UPDATE move_of_vehicles m 
                JOIN department d ON d.id = m.department_id 
                LEFT JOIN remote_sklad r ON r.naklnumb  = m.waybill AND r.date = m.date
                JOIN vehicle v ON v.id=m.vehicle_id
                SET m.remote_sklad_id = r.id
                WHERE m.completed = 1  AND d.name = r.sklad";
        $rowCounts = $this->execute($sql);
        echo "Изменено строк: " . $rowCounts . "\n";
    }
}
