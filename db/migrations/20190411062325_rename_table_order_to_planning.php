<?php

use Phinx\Migration\AbstractMigration;

class RenameTableOrderToPlanning extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('spare_orders');
        $table->rename('spare_planning');

        $table = $this->table('spare_order_items');
        $table->rename('spare_planning_items');

        $table->renameColumn('order_id', 'planning_id');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('spare_planning');
        $table->rename('spare_orders');

        $table = $this->table('spare_planning_items');
        $table->rename('spare_order_items');

        $table->renameColumn('planning_id', 'order_id');
    }
}
