<?php

use Phinx\Migration\AbstractMigration;

class CreateWeighingsTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('weighings')) {
            $table = $this->table('weighings');

            $table->addColumn('waybill', 'integer')
                ->addColumn('date', 'date')
                ->addColumn('time', 'time')
                ->addColumn('comment', 'text')
                ->addColumn('department_id', 'biginteger')
                ->addForeignKey('department_id', 'department')
                ->addColumn('export_id', 'integer')
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable('weighings')) {
            $this->dropTable('weighings');
        }
    }
}
