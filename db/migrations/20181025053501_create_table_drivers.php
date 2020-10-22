<?php

use Phinx\Migration\AbstractMigration;

class CreateTableDrivers extends AbstractMigration
{
    const DEFAULT_ID = 1;
    const DEFAULT_LICENSE = '30077777';
    const DEFAULT_DRIVER_NAME = 'Водитель по умолчанию';

    protected $table = 'drivers';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('name', 'string', ['limit' => 50])
                ->addColumn('license', 'string', ['limit' => 14])
                ->save();

            $date = [
                [
                    'id' => self::DEFAULT_ID,
                    'name' => self::DEFAULT_DRIVER_NAME,
                    'license' => self::DEFAULT_LICENSE,
                ]
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
