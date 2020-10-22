<?php

use Phinx\Migration\AbstractMigration;

class AlterMetalsTableAddAliasColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('metal');
        $table->addColumn('alias', 'string', ['null' => true])
            ->update();
    }
}
