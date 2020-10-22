<?php

use Phinx\Seed\AbstractSeed;

class TariffShipmentSeeder extends AbstractSeed
{
    const COUNT_ROWS = 30; //количество добавляемых строк

    const TYPE_COLOR = 'color';
    const TYPE_BLACK = 'black';

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'tariff_shipment';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    protected $startName = "Тариф отправки №";

    /**
     * Добавляет тестовые данные для тарифов отправки
     *
     */
    public function run()
    {
        $countRows = $this->getCountRows();

        if ($countRows >= self::COUNT_ROWS) {
            return;
        }

        $this->createData($countRows);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param int $countRows
     */
    protected function createData($countRows)
    {
        $data = [];
        $def = (int)$countRows == 0;
        $type = [self::TYPE_COLOR => true, self::TYPE_BLACK => true];
        for ($i = $countRows + 1; $i <= self::COUNT_ROWS; $i++) {
            $data[] = [
                'name' => $this->startName . $i,
                'destination' => $this->startName . $i,
                'distance' => 0,
                'def' => $def,
                'type' => array_rand($type),
                'money' => rand(20000, 100000),

            ];
            $def = 0;
        }
        $this->data = $data;
    }

    /**
     * Возвращает количество строк
     *
     * @return int
     */
    protected function getCountRows()
    {
        $count = $this->query("SELECT count(id) FROM  " . $this->table)->fetchAll();
        return $count[0][0];
    }
}
