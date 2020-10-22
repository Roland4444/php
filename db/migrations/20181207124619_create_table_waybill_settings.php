<?php

use Phinx\Migration\AbstractMigration;

class CreateTableWaybillSettings extends AbstractMigration
{
    const DEFAULT_MECHANIC = 'Свиридов';
    const DEFAULT_DISPATCHER = 'Мустафина';
    const DEFAULT_CHANGE_FACTOR = 1;

    protected $table = 'waybill_settings';

    /**
     * Создание таблицы
     */
    public function up()
    {
        if (! $this->hasTable($this->table)) {
            $table = $this->table($this->table);

            $table->addColumn('name', 'string', ['limit' => 20])
                ->addColumn('value', 'string', ['limit' => 20])
                ->addColumn('label', 'string', ['limit' => 50])
                ->save();

            $data = [
                [
                    'name' => \Modules\Entity\WaybillSettings::DISPATCHER,
                    'value' => self::DEFAULT_DISPATCHER,
                    'label' => 'Диспетчер',
                ],
                [
                    'name' => \Modules\Entity\WaybillSettings::MECHANIC,
                    'value' => self::DEFAULT_MECHANIC,
                    'label' => 'Механик',
                ],
                [
                    'name' => \Modules\Entity\WaybillSettings::CHANGE_FACTOR,
                    'value' => self::DEFAULT_CHANGE_FACTOR,
                    'label' => 'Коэффицент изменения нормы',
                ],
            ];

            $table->insert($data)->save();
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
