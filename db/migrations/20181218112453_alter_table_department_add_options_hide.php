<?php

use Core\Utils\Options;
use Phinx\Migration\AbstractMigration;

class AlterTableDepartmentAddOptionsHide extends AbstractMigration
{
    /**
     * @var string название таблицы
     */
    protected $table = 'department';
    protected $columnVisible = 'visible';

    /**
     * Удаление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnVisible)) {
            $this->setVisibleOption();
            $table->removeColumn($this->columnVisible)->update();
        }
    }

    /**
     * Добавление колоноки
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        if (!$table->hasColumn($this->columnVisible)) {
            $table->addColumn($this->columnVisible, 'integer', [
                'default' => 1,
            ])->update();

            $this->setVisibleOptionInColumn();
        }
    }

    /**
     * Выполняет перенос данных в колонку options
     *
     */
    protected function setVisibleOption()
    {
        $sql = "SELECT options, visible, id FROM {$this->table} WHERE visible = 0 ";
        $data = $this->query($sql)->fetchAll();

        if (empty($data)) {
            return;
        }
        $sql = '';

        foreach ($data as $row) {
            $options = empty($row['options']) ? [] : json_decode($row['options'], true);

            if (!in_array(Options::HIDE, $options)) {
                $options[] = Options::HIDE;
                $options = json_encode($options);
                $sql .= "UPDATE {$this->table} SET options = '{$options}' WHERE id = {$row['id']}; ";
            }
        }

        if (!empty($sql)) {
            $this->query($sql);
        }
    }

    /**
     * Наполняет данными колонку visible из колонки options
     *
     */
    protected function setVisibleOptionInColumn()
    {
        $sql = "SELECT options, id FROM {$this->table}";
        $data = $this->query($sql)->fetchAll();

        if (empty($data)) {
            return;
        }
        $sql = '';

        foreach ($data as $row) {
            $options = empty($row['options']) ? [] : json_decode($row['options'], true);

            if (in_array(Options::HIDE, $options)) {

                $options = array_flip($options);
                unset($options[Options::HIDE]);
                $options = array_flip($options);
                $options = json_encode($options);
                $sql .= "UPDATE {$this->table} SET options = '{$options}', visible = 0 WHERE id = {$row['id']}; ";
            }
        }

        if (!empty($sql)) {
            $this->query($sql);
        }
    }
}
