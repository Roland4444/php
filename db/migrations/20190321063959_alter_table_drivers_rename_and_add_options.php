<?php

use Core\Utils\Options;
use Phinx\Migration\AbstractMigration;

class AlterTableDriversRenameAndAddOptions extends AbstractMigration
{

    /**
     * @var string название таблицы
     */
    protected $tableNameOld = 'drivers';
    protected $tableNameNew = 'employees';

    protected $column = 'options';

    /**
     * Изменение имени таблицы, добавление колонки и наполнение ее данными
     */
    public function up()
    {
        if (! $this->hasTable($this->tableNameOld)) {
            return;
        }

        $table = $this->table($this->tableNameOld);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'json', [
                'default' => null,
                'null' => true,
            ])->rename($this->tableNameNew)
                ->update();

            $sql = "UPDATE {$this->tableNameNew} SET {$this->column} = '" . json_encode([Options::OPTIONS_DRIVER]) . "'";
            $this->query($sql);
        }
    }

    /**
     * Изменение имени таблицы, удаление колонки
     */
    public function down()
    {
        if (! $this->hasTable($this->tableNameNew)) {
            return;
        }
        $table = $this->table($this->tableNameNew);
        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->rename($this->tableNameOld)->update();
        }
    }

}
