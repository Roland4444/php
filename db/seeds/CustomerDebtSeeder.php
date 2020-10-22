<?php

use Phinx\Seed\AbstractSeed;

class CustomerDebtSeeder extends AbstractSeed
{

    const LIMIT_COUNT = 10; //количество строк для выбора случайных покупателей
    const MIN_DATE = '01.01.2010';

    const AMOUNT_MIN = 50000;
    const AMOUNT_MAX = 1000000;

    const MAX_COUNT_ROWS = 20; //Максимальное количество строк при которых будет происходить  посев данных

    protected $table = 'customer_debt';
    protected $tableCustomer = 'customer';

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
        $customers = $this->query($this->getSqlRandId())->fetchAll();

        if (empty($customers)) {
            return;
        }

        $countCustomerDebt = $this->query($this->getSqlCountRow())->fetchAll();
        if ($countCustomerDebt[0][0] >= self::MAX_COUNT_ROWS) {
            return;
        }

        $this->createData($customers);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param  $customers
     */
    protected function createData($customers)
    {
        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                'customer_id' => $customer['id'],
                'amount' => rand(self::AMOUNT_MIN, self::AMOUNT_MAX),
                'date' => date('Y-m-d', rand(strtotime(self::MIN_DATE), time())),
            ];
        }
        $this->data = $data;
    }

    /**
     * Создает строку запроса для получения случайных покупателей
     *
     * @return string
     */
    protected function getSqlRandId()
    {
        return "SELECT id FROM " . $this->tableCustomer . " ORDER BY RAND() LIMIT " . self::LIMIT_COUNT;
    }

    /**
     * Создает строку запроса для получения количества записей с таблице
     *
     * @return string
     */
    protected function getSqlCountRow()
    {
        return "SELECT count(id) FROM " . $this->table;
    }
}
