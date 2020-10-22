<?php

use Phinx\Migration\AbstractMigration;

class CreateTableQueueDefault extends AbstractMigration
{
    protected $table = 'queue_default';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (!$this->hasTable($this->table)) {
            $rootDir = dirname(__DIR__, 2);
            $slmDir = $rootDir . '/vendor/slm/queue-doctrine/data/queue_default.sql';

            if (!file_exists($slmDir)) {
                echo '!!!!!! Для создания таблицы queue_default не найден файл !!!!!!!';
                return;
            }

            $sql = file_get_contents($slmDir);
            $this->query($sql);
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
