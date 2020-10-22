<?php

use Phinx\Migration\AbstractMigration;

class CreateTableSpareInvoice extends AbstractMigration
{
    protected $table = 'spare_invoice';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('document', 'string',
                    [
                        'limit' => 200,
                        'comment' => 'Индефикатор документа прихода'
                    ])
                ->addColumn('provider', 'string',
                    [
                        'limit' => 255,
                        'comment' => 'Поставщик'
                    ])
                ->addColumn('date', 'date',
                    [
                        'comment' => 'Дата поступления'
                    ])
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
