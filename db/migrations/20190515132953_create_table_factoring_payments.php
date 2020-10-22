<?php

use Phinx\Migration\AbstractMigration;

class CreateTableFactoringPayments extends AbstractMigration
{
    private $table = 'factoring_payments';

    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('date', 'date')
                ->addColumn('provider_id', 'integer')
                ->addColumn('money', 'decimal', ['scale' => 2, 'precision' => 10 ])
                ->addForeignKey('provider_id', 'factoring_provider')
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
