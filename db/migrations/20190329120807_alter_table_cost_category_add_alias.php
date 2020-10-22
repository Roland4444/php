<?php

use Phinx\Migration\AbstractMigration;

class AlterTableCostCategoryAddAlias extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'cost_category';
    protected $column = 'alias';

    protected $line = [
      'name' => 'Овердрафт',
      'alias' => 'overdraft',
      'group_id' => 12,
    ];

    /**
     * Добавление колоноки
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->column)) {
            $table->addColumn($this->column, 'string', [
                'limit' => 250,
                'default' => null,
                'null' => true,
            ])->update();
        }
        $sql = "SELECT * FROM " . $this->table . " WHERE alias = '" . $this->line['alias'] . "'";
        if (! empty($this->fetchAll($sql))) {
            return;
        }

        $table->insert($this->line)->save();
    }

    /**
     * Удаление колоноки
     */
    public function down()
    {
        $table = $this->table($this->table);

        $sql = "DELETE FROM " . $this->table . " WHERE alias = '" . $this->line['alias']. "'";
        $this->execute($sql);

        if ($table->hasColumn($this->column)) {
            $table->removeColumn($this->column)->update();
        }
    }
}
