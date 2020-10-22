<?php

use Phinx\Seed\AbstractSeed;

class VehicleSeeder extends AbstractSeed
{
    const COUNT_ROWS = 40; //количество добавляемых строк

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'vehicle';

    /**
     * @var string Таблица для получения данных о депертаментах
     */
    protected $tableDepartment = 'department';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    protected $startName = "Транспорт №";

    /**
     * Добавляет тестовые данные для транспортных средств
     *
     */
    public function run()
    {
        $countRows = $this->getCountRows();

        if ($countRows >= self::COUNT_ROWS) {
            return;
        }

        $departmentId = $this->getDepartment();

        if (empty($departmentId)) {
            return;
        }

        $this->createData($departmentId, $countRows);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева транспорта
     *
     * @param array $departmentId
     * @param int $countRows
     */
    protected function createData($departmentId, $countRows)
    {
        $data = [];
        $number = $countRows;
        foreach ($departmentId as $department) {
            $countVehicle = rand(2, 4);
            for ($i = 1; $i <= $countVehicle; $i++) {
                $data[] = [
                    'name' => $this->startName . ++$number,
                    'department_id' => $department,
                    'movable' => rand(0, 1),
                ];
                if ($number >= self::COUNT_ROWS){
                    break 2;
                }
            }
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

    /**
     * Возвращает данные об отделениях
     *
     * @return array
     */
    protected function getDepartment()
    {
        $departmentId = [];
        $departments = $this->query("SELECT id FROM  " . $this->tableDepartment)->fetchAll();

        if (empty($departments)) {
            return $departmentId;
        }

        foreach ($departments as $department) {
            $departmentId[] = $department['id'];
        }

        return $departmentId;
    }
}
