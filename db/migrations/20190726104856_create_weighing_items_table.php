<?php

use Phinx\Migration\AbstractMigration;

class CreateWeighingItemsTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('weighing_items')) {
            $table = $this->table('weighing_items');

            $table->addColumn('trash', 'decimal', [
                    'precision' => 10,
                    'scale' => 2
                ])
                ->addColumn('clogging', 'decimal', [
                    'precision' => 10,
                    'scale' => 2
                ])
                ->addColumn('tare', 'decimal', [
                    'precision' => 10,
                    'scale' => 2
                ])
                ->addColumn('brutto', 'decimal', [
                    'precision' => 10,
                    'scale' => 2
                ])
                ->addColumn('metal_id', 'biginteger')
                ->addForeignKey('metal_id', 'metal')
                ->addColumn('weighing_id', 'integer')
                ->addForeignKey('weighing_id', 'weighings')
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable('weighing_items')) {
            $this->dropTable('weighing_items');
        }
    }
}
