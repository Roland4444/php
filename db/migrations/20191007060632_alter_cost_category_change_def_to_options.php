<?php

use Phinx\Migration\AbstractMigration;

class AlterCostCategoryChangeDefToOptions extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('cost_category');
        $table->removeColumn('def')
            ->addColumn('options', 'json', [
                'default' => null,
                'null' => true
            ])
            ->update();
    }
}
