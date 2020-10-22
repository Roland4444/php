<?php

use Phinx\Seed\AbstractSeed;

class MainReceiptsSeeder extends AbstractSeed
{
    const COUNT_ROWS = 200; //количество добавляемых строк
    const START_DATE = '2018-05-01';

    const MAX_SUMM = 500000;
    const MIN_SUMM = 1000;

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'main_receipts';
    protected $tableBankAccount = 'bank_account';
    protected $nameStart = 'Отправитель №';
    /**
     * @var array Комментарии для добавления
     */
    protected $comments = [
        'Все хорошо', 'Все отлично', 'Работает', 'Все прошло',
    ];

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
        $countRows = $this->getCount();

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
        $data = [];
        $idsBankAccount = $this->getIdsBankAccount();

        if (empty($idsBankAccount)) {
            return;
        }

        $this->comments = array_flip($this->comments);

        for ($i = $countRows + 1; $i <= self::COUNT_ROWS; $i++) {
            $data[] = [
                'date' => date('Y-m-d', rand(strtotime(self::START_DATE), time())),
                'money' => rand(self::MIN_SUMM, self::MAX_SUMM),
                'comment' => $this->getComments(),
                'payment_number' => null,
                'bank_account_id' => array_rand($idsBankAccount),
                'inn' => rand(301000000000, 301999999999),
                'sender' => $this->nameStart . $i,
            ];
        }

        $this->data = $data;
    }

    /**
     * Возвращает количество данных в таблице
     *
     * @return int
     */
    protected function getCount()
    {
        $sql = "SELECT count(id) FROM  " . $this->table;
        return $this->query($sql)->fetchAll()[0][0];
    }

    /**
     * Возвращает id по таблице банковских аккаунтов
     *
     * @return array
     */
    protected function getIdsBankAccount()
    {
        $result = [];

        $sql = "SELECT id FROM  " . $this->tableBankAccount;
        $bankAccountIds = $this->query($sql)->fetchAll();

        if (empty($bankAccountIds)) {
            return $result;
        }

        foreach ($bankAccountIds as $accountId) {
            $result[$accountId['id']] = true;
        }

        return $result;
    }

    /**
     * Гениратор случайных комментарий
     *
     * @return string
     */
    protected function getComments(){
        $comment = '';
        if (rand(0, 5) > 3) {
            $comment = array_rand($this->comments);
        }

        return $comment;
    }
}
