<?php

use Phinx\Migration\AbstractMigration;

class AlterMainOtherExpensesInnToString extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('main_other_expenses');
        $table->changeColumn('inn', 'string', ['limit' => 20, 'null' => true])
            ->update();
    }
}
