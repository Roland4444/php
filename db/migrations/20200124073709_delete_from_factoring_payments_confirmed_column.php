<?php

use Phinx\Migration\AbstractMigration;

class DeleteFromFactoringPaymentsConfirmedColumn extends AbstractMigration
{
    public function change()
    {
        $this->table('factoring_payments')
            ->removeColumn('confirmed')
            ->update();
    }
}
