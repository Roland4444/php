<?php

use Phinx\Migration\AbstractMigration;

class CreateTableFactoringAssignmentDebt extends AbstractMigration
{
    private $table = 'factoring_assignment_debt';

    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('date', 'date')
                ->addColumn('provider_id', 'integer')
                ->addColumn('trader_id', 'biginteger', ['limit' => 20])
                ->addColumn('money', 'decimal', ['scale' => 2, 'precision' => 10 ])
                ->addForeignKey('provider_id', 'factoring_provider')
                ->addForeignKey('trader_id', 'trader')
                ->save();
        }
    }

    /**
     * Удаление таблицы
     */
    public function down()
    {
        if ($this->hasTable($this->table)) {
            $this->dropTable($this->table);
        }
    }
}
