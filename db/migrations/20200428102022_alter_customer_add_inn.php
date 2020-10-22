<?php

use Phinx\Migration\AbstractMigration;

class AlterCustomerAddInn extends AbstractMigration
{
    public function change()
    {
        $this->table('customer')
            ->addColumn('inn', 'string', ['limit' => 20])
            ->update();
    }
}
