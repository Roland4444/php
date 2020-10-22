<?php

use Phinx\Seed\AbstractSeed;

class PurchaseSeeder extends AbstractSeed
{
    const LIMIT_COUNT = 20; //количество строк для выбора случайных покупателей
    const MIN_DATE = '01.10.2018';

    const COST_MIN = 10;
    const COST_MAX = 300;

    const WEIGHT_MIN = 10;
    const WEIGHT_MAX = 300;

    const MAX_COUNT_ROWS = 60; //Максимальное количество строк при которых будет происходить  посев данных

    protected $table = 'purchase';
    protected $tableCustomer = 'customer';
    protected $tableDepartment = 'department';
    protected $tableMetal = 'metal';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    /**
     * Добавляет тестовые данные оплат поставщиков
     *
     */
    public function run()
    {
        $countRow = $this->getSqlCountRow();

        if ($countRow >= self::MAX_COUNT_ROWS) {
            return;
        }

        $this->createData($countRow);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert($this->data)->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param  $countRow
     */
    protected function createData($countRow)
    {
        $data = [];

        $departments = $this->getRandId($this->tableDepartment);
        if (empty($departments)) {
            return;
        }

        $customers = $this->getRandId($this->tableCustomer, true);
        if (empty($customers)) {
            return;
        }

        $metals = $this->getRandId($this->tableMetal, true);
        if (empty($metals)) {
            return;
        }

        for ($i = $countRow + 1; $i <= self::MAX_COUNT_ROWS; $i++){
            $data[] = [
                'date' => date('Y-m-d', rand(strtotime(self::MIN_DATE), time())),
                'weight' => rand(self::WEIGHT_MIN, self::WEIGHT_MAX),
                'cost' => rand(self::COST_MIN, self::COST_MAX),
                'formal_cost' => rand(self::COST_MIN, self::COST_MAX),
                'metal_id' => array_rand($metals),
                'department_id' => array_rand($departments),
                'customer_id' => array_rand($customers),
            ];
        }

        $this->data = $data;
    }

    /**
     * Возвращает выборки данных
     *
     * @param $table
     * @param bool $rand
     * @return array
     */
    protected function getRandId($table, $rand = false)
    {
        $result = [];
        $sql = "SELECT id FROM " . $table;

        if ($rand) {
            $sql = $sql . " ORDER BY RAND() LIMIT " . self::LIMIT_COUNT;
        }

        $response = $this->query($sql)->fetchAll();
        if (empty($response)) {
            return $result;
        }

        foreach ($response as $value) {
            $result[$value['id']] = true;
        }

        return $result;
    }

    /**
     * Количество записей с таблице
     *
     * @return string
     */
    protected function getSqlCountRow()
    {
        $sql = "SELECT count(id) FROM " . $this->table;
        $result = $this->query($sql)->fetchAll();
        return $result[0][0];
    }
}
