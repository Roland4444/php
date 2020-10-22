<?php

use Phinx\Migration\AbstractMigration;

class AddTableSpareTransfer extends AbstractMigration
{
    protected $table = 'spare_transfer';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if ($this->hasTable($this->table)) {
            return;
        }

        $table = $this->table($this->table);

        $table->addColumn('warehouse_exp_id', 'integer', [
            'limit' => 11,
            'comment' => 'id подразделение с которого переводят'
        ])
            ->addColumn('warehouse_imp_id', 'integer', [
                'limit' => 11,
                'comment' => 'id подразделение на которое переводят'
            ])
            ->addColumn('spare_id', 'biginteger', [
                'limit' => 20,
                'comment' => 'id запчасти',
                'signed' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'limit' => 11,
                'comment' => 'Количество товара',
            ])->addColumn('sub_quantity', 'integer', [
                'null' => true,
                'limit' => 11,
                'default' => NULL,
                'comment' => 'Количество единиц (штуки, литры итп) в одном пришедшем товаре'
            ])
            ->addColumn('date', 'date', [
                'comment' => 'Дата перевода'
            ])
            ->addForeignKey('spare_id', 'spares', ['id'])
            ->addForeignKey('warehouse_exp_id', 'warehouse', ['id'])
            ->addForeignKey('warehouse_imp_id', 'warehouse', ['id'])
            ->save();
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
