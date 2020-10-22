<?php

use Phinx\Seed\AbstractSeed;

class DepartmentSeeder extends AbstractSeed
{
    //Типы принимаемого металла
    const TYPE_BLACK = 'black'; //черный металл
    const TYPE_COLOR = 'color'; //цветной металл

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'department';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [
        1 => [
            'id' => 1,
            'type' => self::TYPE_BLACK,
            'name' => 'АЦКК',
            'source_department' => NULL,
            'alias' => NULL,
        ],
        2 => [
            'id' => 2,
            'type' => self::TYPE_BLACK,
            'name' => 'Бабаевского',
            'source_department' => NULL,
            'alias' => NULL,
        ],
        3 => [
            'id' => 3,
            'type' => self::TYPE_BLACK,
            'name' => 'Кутум',
            'source_department' => NULL,
            'alias' => NULL,
        ],
        4 => [
            'id' => 4,
            'type' => self::TYPE_COLOR,
            'name' => 'Цветной опт',
            'source_department' => NULL,
            'alias' => NULL,
        ],
        5 => [
            'id' => 5,
            'type' => self::TYPE_COLOR,
            'name' => 'Цветной Рождественского',
            'source_department' => NULL,
            'alias' => 'kutum_color',
        ],
        6 => [
            'id' => 6,
            'type' => self::TYPE_BLACK,
            'name' => 'Стрелецкий терминал',
            'source_department' => 7,
            'alias' => NULL,
        ],
        7 => [
            'id' => 7,
            'type' => self::TYPE_BLACK,
            'name' => 'Центральный Грузовой',
            'source_department' => 7,
            'alias' => NULL,
        ],
        8 => [
            'id' => 8,
            'type' => self::TYPE_BLACK,
            'name' => 'ВТЗ',
            'source_department' => NULL,
            'alias' => 'vtz',
        ],
        9 => [
            'id' => 9,
            'type' => self::TYPE_BLACK,
            'name' => 'Оператор касса',
            'source_department' => NULL,
            'alias' => 'officecash',
        ],
        10 => [
            'id' => 10,
            'type' => self::TYPE_COLOR,
            'name' => 'Новая база цветного металла',
            'source_department' => NULL,
            'alias' => Null,
        ],
        11 => [
            'id' => 11,
            'type' => self::TYPE_COLOR,
            'name' => 'Еще одна цветного металла',
            'source_department' => NULL,
            'alias' => Null,
        ],
    ];

    /**
     * Добавляет тестовые данные для департаментов
     */
    public function run()
    {
        $departments = $this->query($this->getQueryString())->fetchAll();

        foreach ($departments as $department) {
            if (isset($this->data[$department['id']])) {
                unset($this->data[$department['id']]);
            }
        }

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Возвращает строку запроса для поиска имеющихся департаментов
     *
     * @return string
     */
    protected function getQueryString()
    {
        return 'SELECT id FROM ' . $this->table;
    }
}
