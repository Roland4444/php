<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareConsumptionSetNullForVehicleId extends AbstractMigration
{
    protected $table = 'spare_consumption';
    protected $column = 'vehicle_id';

    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table->changeColumn($this->column, 'biginteger', [
            'limit' => 20,
            'comment' => 'id техники',
            'null' => true,
            'default' => null,
        ])->save();
    }

    public function down()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            return;
        }

        $table->dropForeignKey($this->column)
            ->removeColumn($this->column)
            ->save();

        $table->addColumn($this->column, 'biginteger', [
            'limit' => 20,
            'comment' => 'id техники',
            'null' => false,
        ])->addForeignKey($this->column, 'vehicle', ['id'])
            ->save();
    }
}
