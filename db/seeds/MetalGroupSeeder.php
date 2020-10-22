<?php

use Phinx\Seed\AbstractSeed;

class MetalGroupSeeder extends AbstractSeed
{

    protected $table = 'metal_group';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [
        5 => ['id' => 5,
            'name' => 'Черный',
            'ferrous' => 1],
        6 => ['id' => 6,
            'name' => 'Красная',
            'ferrous' => 0],
        7 => ['id' => 7,
            'name' => 'Аллюминий',
            'ferrous' => 0],
        8 => ['id' => 8,
            'name' => 'Желтая',
            'ferrous' => 0],
        9 => ['id' => 9,
            'name' => 'Никелесодержащие',
            'ferrous' => 0],
        10 => ['id' => 10,
            'name' => 'АКБ',
            'ferrous' => 0],
        11 => ['id' => 11,
            'name' => 'Свинец',
            'ferrous' => 0],
        12 => ['id' => 12,
            'name' => 'Стружка',
            'ferrous' => 0],
        13 => ['id' => 13,
            'name' => 'Стружка медная',
            'ferrous' => 0],
        14 => ['id' => 14,
            'name' => 'Цинк',
            'ferrous' => 0],
        15 => ['id' => 15,
            'name' => 'Радиатор медный',
            'ferrous' => 0],
        16 => ['id' => 16,
            'name' => 'Олово',
            'ferrous' => 0],
        17 => ['id' => 17,
            'name' => 'Монель',
            'ferrous' => 0],
        18 => ['id' => 18,
            'name' => 'Нихром',
            'ferrous' => 0],
        19 => ['id' => 19,
            'name' => 'Бабит',
            'ferrous' => 0],
        20 => ['id' => 20,
            'name' => 'Цам',
            'ferrous' => 0],
        21 => ['id' => 21,
            'name' => 'Ферованадий',
            'ferrous' => 0],
        22 => ['id' => 22,
            'name' => 'ВК ТК',
            'ferrous' => 0],
        23 => ['id' => 23,
            'name' => 'никель',
            'ferrous' => 0],
        24 => ['id' => 24,
            'name' => 'Магний',
            'ferrous' => 0],
        25 => ['id' => 25,
            'name' => 'Электронный лом',
            'ferrous' => 0],
    ];

    /**
     * Добавляет тестовые данные для групп металлов
     *
     */
    public function run()
    {
        $metals = $this->query($this->getQueryString())->fetchAll();

        foreach ($metals as $metal) {
            if (isset($this->data[$metal['id']])) {
                unset($this->data[$metal['id']]);
            }
        }

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Возвращает строку запроса для поиска имеющихся групп металлов
     *
     * @return string
     */
    protected function getQueryString()
    {
        return "SELECT id FROM " . $this->table .
            " WHERE id IN ('" . implode("', '", array_keys($this->data)) . "')";
    }
}
