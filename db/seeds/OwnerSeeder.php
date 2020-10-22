<?php

use Phinx\Seed\AbstractSeed;

class OwnerSeeder extends AbstractSeed
{
    const COUNT_ROWS = 30; //количество добавляемых строк

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'owner';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    protected $startName = "Собственник №";

    /**
     * Добавляет тестовые данные для собственников
     *
     */
    public function run()
    {
        $innBd = $this->query($this->getQueryString())->fetchAll();

        if (count($innBd) >= self::COUNT_ROWS) {
            return;
        }

        $this->createData($innBd);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param array $innBd
     */
    protected function createData($innBd)
    {
        $data = [];
        $inns = $this->getInns($innBd);
        for ($i = 1; $i <= self::COUNT_ROWS; $i++) {
            while (true) {
                $inn = rand(301000000000, 301999999999);
                if (!isset($inns[$inn])) {
                    $inns[$inn] = true;
                    break;
                }
            }
            $data[] = [
                'name' => $this->startName . $i,
                'inn' => $inn,
            ];
        }
        $this->data = $data;
    }

    /**
     * Возвращает строку запроса
     *
     * @return string
     */
    protected function getQueryString()
    {
        return "SELECT inn FROM  " . $this->table;
    }

    /**
     * Возвращает массив с хранящимися инн в базе
     *
     * @param $innBd
     * @return array
     */
    protected function getInns($innBd)
    {
        $inns = [];
        if (empty($innBd)) {
            return $inns;
        }

        foreach ($innBd as $inn) {
            $inns[$inn['inn']] = true;
        }

        return $inns;
    }
}
