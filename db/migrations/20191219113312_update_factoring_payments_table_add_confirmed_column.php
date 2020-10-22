<?php

use Phinx\Migration\AbstractMigration;

class UpdateFactoringPaymentsTableAddConfirmedColumn extends AbstractMigration
{
    public function change()
    {
        $this->table('factoring_payments')
            ->addColumn('confirmed', 'integer', [
                'limit' => 1,
                'default' => 0,
                'null' => true
            ])
            ->update();
    }
}
