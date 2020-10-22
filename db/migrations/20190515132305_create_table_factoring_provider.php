<?php

use Phinx\Migration\AbstractMigration;

class CreateTableFactoringProvider extends AbstractMigration
{
    private $table = 'factoring_provider';

    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('title', 'string', ['limit' => 250])
                ->addColumn('inn', 'string', ['limit' => 20])
                ->save();
            $date = [
                [
                    'title' => 'ООО "Сбербанк Факторинг"',
                    'inn' => '7802754982',
                ],
            ];
            $table->insert($date)->save();
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
