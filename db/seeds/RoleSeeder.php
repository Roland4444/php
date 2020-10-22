<?php

use Phinx\Seed\AbstractSeed;

class RoleSeeder extends AbstractSeed
{
    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'role';

    public function run()
    {
        $updateData = [];
        $data = [
            1 => [
                'id' => '1',
                'name' => 'admin',
            ],
            2 => [
                'id' => '2',
                'name' => 'sklad',
            ],
            3 => [
                'id' => '3',
                'name' => 'minisklad',
            ],
            4 => [
                'id' => '4',
                'name' => 'viewer',
            ],
            9 => [
                'id' => '9',
                'name' => 'vehicle',
            ],
            10 => [
                'id' => '10',
                'name' => 'colorSklad',
            ],
            11 => [
                'id' => '11',
                'name' => 'colorMiniSklad',
            ],
            12 => [
                'id' => '12',
                'name' => 'miniSkladViewer',
            ],
            13 => [
                'id' => '13',
                'name' => 'security',
            ],
            14 => [
                'id' => '14',
                'name' => 'glavbuh',
            ],
            15 => [
                'id' => '15',
                'name' => 'officecash',
            ],
            16 => [
                'id' => '16',
                'name' => 'NonFerrousShipment',
            ],
            17 => [
                'id' => '17',
                'name' => 'NonFerrousCash',
            ],
            18 => [
                'id' => '18',
                'name' => 'NonFerrousStorage',
            ],
        ];

        $roles = $this->query('SELECT id, name FROM ' . $this->table)->fetchAll();

        foreach ($roles as $role) {
            if (isset($data[$role['id']])) {
                if ($data[$role['id']]['name'] != $role['name']) {
                    $updateData[$role['id']] = $data[$role['id']]['name'];
                }
                unset($data[$role['id']]);
            }
        }

        //Проверяем стоит ли что-то добавлять
        if (!empty($data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($data))
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
