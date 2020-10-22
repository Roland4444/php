<?php

use Phinx\Seed\AbstractSeed;

class CostCategoryGroupSeeder extends AbstractSeed
{
    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'cost_category_group';

    /**
     * @var array Данные для заполнения
     */
    protected $data = [
        5 => [
            'id' => 5,
            'name' => 'Аренда'
        ],
        6 => [
            'id' => 6,
            'name' => 'Девиденды'
        ],
        7 => [
            'id' => 7,
            'name' => 'Железная дорога'
        ],
        8 => [
            'id' => 8,
            'name' => 'Зарплаты'
        ],
        9 => [
            'id' => 9,
            'name' => 'Лизинг'
        ],
        10 => [
            'id' => 10,
            'name' => 'Охрана'
        ],
        11 => [
            'id' => 11,
            'name' => 'Представительские'
        ],
        12 => [
            'id' => 12,
            'name' => 'Прочие'
        ],
        13 => [
            'id' => 13,
            'name' => 'Связь'
        ],
        14 => [
            'id' => 14,
            'name' => 'Техника'
        ],
        15 => [
            'id' => 15,
            'name' => 'Электроэнергия'
        ],

    ];

    /**
     * Производит заполнение таблицы категории групп
     *
     */
    public function run()
    {
        $updateData = [];

        $categoriesGroup = $this->query('SELECT id, name FROM ' . $this->table)->fetchAll();

        foreach ($categoriesGroup as $categoryGroup) {
            if (isset($this->data[$categoryGroup['id']])) {
                if ($this->data[$categoryGroup['id']]['name'] != $categoryGroup['name']) {
                    $updateData[$categoryGroup['id']] = $this->data[$categoryGroup['id']]['name'];
                }
                unset($this->data[$categoryGroup['id']]);
            }
        }

        //Проверяем стоит ли что-то добавлять
        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))
                ->save();
        }

        //Если есть что изменять - создаем запросы
        if (!empty($updateData)) {
            $sql = [];
            foreach ($updateData as $id => $insertDate) {
                $sql[] = "UPDATE " . $this->table . " SET name = '" . $insertDate . "' WHERE id = " . $id;
            }

            $this->query(implode('; ', $sql));
        }
    }
}
