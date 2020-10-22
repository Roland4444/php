<?php

use Phinx\Seed\AbstractSeed;

class BankAccountSeeder extends AbstractSeed
{
    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'bank_account';

    /**
     * @var array Данные для заполнения
     */
    protected $data = [
        8 => [
            'id' => 8,
            'name' => 'Наличные',
            'bank' => 'Наличные',
            'cash' => 1,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        9 => [
            'id' => 9,
            'name' => '40702810747060000108',
            'bank' => 'Уралсиб',
            'cash' => 0,
            'def' => 0,
            'closed' => 1,
            'diamond' => 0,
        ],
        10 => [
            'id' => 10,
            'name' => '40702810947060000351',
            'bank' => 'Уралсиб',
            'cash' => 0,
            'def' => 0,
            'closed' => 1,
            'diamond' => 0,
        ],
        11 => [
            'id' => 11,
            'name' => '40702810647069000021',
            'bank' => 'Уралсиб',
            'cash' => 0,
            'def' => 0,
            'closed' => 1,
            'diamond' => 0,
        ],
        12 => [
            'id' => 12,
            'name' => '40702810805000031533',
            'bank' => 'Сбербанк',
            'cash' => 0,
            'def' => 1,
            'closed' => 0,
            'diamond' => 0,
        ],
        13 => [
            'id' => 13,
            'name' => '40702810901000014729',
            'bank' => 'ПромСвязьБанк',
            'cash' => 0,
            'def' => 0,
            'closed' => 1,
            'diamond' => 0,
        ],
        14 => [
            'id' => 14,
            'name' => '40702810605000002296',
            'bank' => 'Сбербанк',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        15 => [
            'id' => 15,
            'name' => '40702810105000031534',
            'bank' => 'Сбербанк',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        16 => [
            'id' => 16,
            'name' => '40702810046300000156',
            'bank' => 'Россельхозбанк',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        17 => [
            'id' => 17,
            'name' => '40702810305000002884',
            'bank' => 'Сбербанк',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        18 => [
            'id' => 18,
            'name' => '40702810636100031205',
            'bank' => 'Авангард',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 0,
        ],
        19 => [
            'id' => 19,
            'name' => 'РНКО',
            'bank' => 'Единая касса',
            'cash' => 0,
            'def' => 0,
            'closed' => 0,
            'diamond' => 1,
        ],
    ];

    /**
     * Производит заполнение данными таблицу банковских аккаунтов
     *
     */
    public function run()
    {
        $bankAccounts = $this->query('SELECT id, name, bank, cash, def, closed, diamond  FROM ' . $this->table)
                             ->fetchAll();

        $updateData = $this->getUpdateDate($bankAccounts);

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
                $sql[] = "UPDATE " . $this->table .
                    " SET name = '" . $insertDate['name'] ."'" .
                    ", bank = '". $insertDate['bank'] ."'" .
                    ", cash = '". $insertDate['cash'] ."'" .
                    ", def = '". $insertDate['def'] ."'" .
                    ", closed = '". $insertDate['closed'] ."'" .
                    ", diamond = '". $insertDate['diamond'] ."'" .
                    " WHERE id = " . $id;
            }

            $this->query(implode('; ', $sql));
        }
    }

    /**
     * Возвращает данные, которорые следует обнавить
     *
     * @param $bankAccounts
     * @return array
     */
    private function getUpdateDate($bankAccounts){
        $updateData = [];
        if (empty($bankAccounts)){
            return $updateData;
        }
        foreach ($bankAccounts as $bankAccount) {
            if (isset($this->data[$bankAccount['id']])) {
                $isFalseName =  $this->data[$bankAccount['id']]['name'] != $bankAccount['name'];
                $isFalseBank =  $this->data[$bankAccount['id']]['bank'] != $bankAccount['bank'];
                $isFalseCash =  $this->data[$bankAccount['id']]['cash'] != $bankAccount['cash'];
                $isFalseDef =  $this->data[$bankAccount['id']]['def'] != $bankAccount['def'];
                $isFalseClosed =  $this->data[$bankAccount['id']]['closed'] != $bankAccount['closed'];
                $isFalseDiamond =  $this->data[$bankAccount['id']]['diamond'] != $bankAccount['diamond'];

                if ($isFalseName || $isFalseBank || $isFalseCash || $isFalseDef || $isFalseClosed || $isFalseDiamond) {
                    $updateData[$bankAccount['id']]['name'] = $this->data[$bankAccount['id']]['name'];
                    $updateData[$bankAccount['id']]['bank'] = $this->data[$bankAccount['id']]['bank'];
                    $updateData[$bankAccount['id']]['cash'] = $this->data[$bankAccount['id']]['cash'];
                    $updateData[$bankAccount['id']]['def'] = $this->data[$bankAccount['id']]['def'];
                    $updateData[$bankAccount['id']]['closed'] = $this->data[$bankAccount['id']]['closed'];
                    $updateData[$bankAccount['id']]['diamond'] = $this->data[$bankAccount['id']]['diamond'];
                }
                unset($this->data[$bankAccount['id']]);
            }
        }

        return $updateData;
    }
}
