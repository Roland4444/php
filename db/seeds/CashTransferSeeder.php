<?php

use Phinx\Seed\AbstractSeed;

class CashTransferSeeder extends AbstractSeed
{
    const COUNT_ROWS = 200; //количество добавляемых строк
    const START_DATE = '2018-05-01';

    const MAX_SUMM = 500000;
    const MIN_SUMM = 1000;

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'cash_transfer';
    protected $tableBankAccount = 'bank_account';

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

        for ($i = $countRows + 1; $i <= self::COUNT_ROWS; $i++) {
            $accounts = $this->getAccounts($idsBankAccount);
            $data[] = [
                'source_id' => $accounts['sourceId'],
                'dest_id' => $accounts['destId'],
                'money' => rand(self::MIN_SUMM, self::MAX_SUMM),
                'payment_number' => null,
                'date' => date('Y-m-d', rand(strtotime(self::START_DATE), time())),
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
     * Генерирует информацию о счете с кокого на какой пойдут перечисления
     *
     * @param array $idsBankAccount
     * @return array
     */
    protected function getAccounts($idsBankAccount)
    {
        $sourceId = array_rand($idsBankAccount);
        while (true) {
            $destId = array_rand($idsBankAccount);
            if ($sourceId != $destId) {
                break;
            }
        }

        return ['sourceId' => $sourceId, 'destId' => $destId];
    }
}
