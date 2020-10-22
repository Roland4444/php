<?php

use Phinx\Migration\AbstractMigration;

class AlretTableSpareOrderAlterComment extends AbstractMigration
{
    protected $table = 'spare_orders';
    protected $column = 'warehouse_id';

    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn('date_create')) {
            return;
        }

        $table->removeColumn('date_create')
            ->removeColumn('storage_comment')
            ->removeColumn('supplier_comment')
            ->update();

        $table->addColumn('comment', 'string', [
            'default' => null,
            'null' => true,
            'limit' => 250,
            'comment' => 'Комментарий',
        ])->addColumn('date', 'date')->save();
    }

    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn('date_create')) {
            return;
        }

        $table->removeColumn('comment')
            ->removeColumn('date')
            ->update();

        $table->addColumn('date_create', 'date')
            ->addColumn('storage_comment', 'string', [
                'limit' => 250,
                'comment' => 'Комментарий кладовщика',
            ])->addColumn('supplier_comment', 'string', [
                'limit' => 250,
                'comment' => 'Комментарий снабженца',
            ])->update();
    }
}
