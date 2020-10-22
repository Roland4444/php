<?php

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class SparesSeeder extends AbstractSeed
{
    const COUNT_ROWS = 150; //количество добавляемых строк

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'spares';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    protected $startName = "Запчасть №";

    /**
     * Добавляет тестовые данные для запчастей
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
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = $countRows + 1; $i <= self::COUNT_ROWS; $i++) {
            $data[] = [
                'name' => $this->startName . $i,
                'comment' => $faker->text(15),
            ];
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
