<?php

use Core\Utils\Options;
use Phinx\Migration\AbstractMigration;

class AlterTableTraderParentAddOption extends AbstractMigration
{
    protected $table = 'trader_parent';
    protected $columnHide = Options::HIDE;
    protected $columnOptions = 'options';

    /**
     * Добавление поля options. Наполнение его
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnHide)) {
            if (!$table->hasColumn($this->columnOptions)) {
                $table->addColumn($this->columnOptions, 'json', [
                    'default' => null,
                    'null' => true,
                ])->update();
            }
            $this->setHideOptions();
            $table->removeColumn($this->columnHide)->update();
        }
    }

    /**
     * Удаление поля options и создает поле hide
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->columnHide) && $table->hasColumn($this->columnOptions)) {
            $table->addColumn($this->columnHide, 'integer', [
                'default' => 0,
                'limit' => 1,
            ])->update();
            $this->revertHideOption();
            if ($table->hasColumn($this->columnOptions)) {
                $table->removeColumn($this->columnOptions)->update();
            }
        }
    }

    /**
     * Собирает данные и сохраняет их в колонку опций
     *
     */
    protected function setHideOptions()
    {
        $sql = "SELECT {$this->columnHide}, id FROM {$this->table} WHERE {$this->columnHide} = 1 ";
        $data = $this->query($sql)->fetchAll();
        if (empty($data)) {
            return;
        }
        $sql = '';
        foreach ($data as $row) {
            $options = [Options::HIDE];
            $options = json_encode($options);
            $sql .= "UPDATE {$this->table} SET options = '{$options}' WHERE id = {$row['id']}; ";
        }
        if (!empty($sql)) {
            $this->query($sql);
        }
    }

    /**
     * Сохраняяет данные о скрытых полях и устанавливает их в поле hide
     *
     */
    protected function revertHideOption()
    {
        $sql = "SELECT {$this->columnOptions}, id FROM {$this->table}";
        $data = $this->query($sql)->fetchAll();
        if (empty($data)) {
            return;
        }
        $sql = '';
        foreach ($data as $row) {
            $options = empty($row['options']) ? [] : json_decode($row['options'], true);
            $hide = Options::HIDE;
            $hideValue = 0;

            if (in_array($hide, $options)) {
                $hideValue = 1;
            }
            $sql .= "UPDATE {$this->table} SET {$this->columnHide} = $hideValue WHERE id = {$row['id']}; ";
        }
        if (!empty($sql)) {
            $this->query($sql);
        }
    }
}
