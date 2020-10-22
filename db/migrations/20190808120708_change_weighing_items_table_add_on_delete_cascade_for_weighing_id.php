<?php

use Phinx\Migration\AbstractMigration;

class ChangeWeighingItemsTableAddOnDeleteCascadeForWeighingId extends AbstractMigration
{
    public function change()
    {
        if ($this->hasTable('weighing_items')) {
            $table = $this->table('weighing_items');

            $table->dropForeignKey('weighing_id')->save();

            $table->addForeignKey('weighing_id', 'weighings', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->save();
        }
    }
}
