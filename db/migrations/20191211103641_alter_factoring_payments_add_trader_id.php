<?php

use Phinx\Migration\AbstractMigration;

class AlterFactoringPaymentsAddTraderId extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('factoring_payments');
        $table->addColumn('trader_id', 'biginteger', ['null' => true])
            ->addForeignKey('trader_id', 'trader')
            ->update();
    }
}
