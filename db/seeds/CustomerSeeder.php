<?php

use Phinx\Seed\AbstractSeed;

class CustomerSeeder extends AbstractSeed
{
    const COUNT_ROWS = 200; //количество добавляемых строк

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'customer';
    protected $startName = 'Поставщик №';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    /**
     * Добавляет тестовые данные для поставщиков
     *
     */
    public function run()
    {
        $customers = $this->query($this->getQueryString())->fetchAll();

        if ($customers[0][0] >= self::COUNT_ROWS) {
            return;
        }
        $this->createData($customers[0][0]);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param int $customersCount
     */
    protected function createData($customersCount)
    {
        $data = [];
        $def = (int)$customersCount == 0;
        for ($i = 1; $i <= self::COUNT_ROWS; $i++) {
            $data[] = [
                'name' => $this->startName . $i,
                'def' => $def,
                'legal' => array_rand([1, 2]),
            ];
            $def = 0;
        }
        $this->data = $data;
    }

    /**
     * Возвращает строку запроса для получения количества поставщиков
     *
     * @return string
     */
    protected function getQueryString()
    {
        return "SELECT count(id) FROM  " . $this->table;
    }
}