<?php

use Phinx\Migration\AbstractMigration;

class AlterMetalTableAddCodeColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('metal');
        
        $table
            ->addColumn('code', 'string')
            ->save();
    }
}
