<?php

use Phinx\Migration\AbstractMigration;

class AlterSparesAddUnits extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('spares');
        $table->addColumn('units', 'string', ['limit' => 10])
            ->update();
    }
}
