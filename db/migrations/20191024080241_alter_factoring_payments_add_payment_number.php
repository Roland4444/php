<?php

use Phinx\Migration\AbstractMigration;

class AlterFactoringPaymentsAddPaymentNumber extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('factoring_payments');
        $table->addColumn('payment_number', 'integer', [
            'default' => null,
            'null' => true,
        ])->update();
    }
}
