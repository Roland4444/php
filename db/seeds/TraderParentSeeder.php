<?php

use Phinx\Seed\AbstractSeed;

class TraderParentSeeder extends AbstractSeed
{
    const PARENT_BLACK = 'black';

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'trader_parent';

    protected $data = [
        1 => [
            'id' => 1,
            'name' => 'Остальные',
            'ord' => 5,
            'alias' => NULL],
        2 => [
            'id' => 2,
            'name' => 'Урал закрытые',
            'ord' => 4,
            'alias' => NULL],
        5 => [
            'id' => 5,
            'name' => 'Чермет',
            'ord' => 1,
            'alias' => self::PARENT_BLACK],
        6 => [
            'id' => 6,
            'name' => 'Цветмет',
            'ord' => 2,
            'alias' => NULL],
        7 => [
            'id' => 7,
            'name' => 'Аллюминий',
            'ord' => 3,
            'alias' => NULL],
        8 => [
            'id' => 8,
            'name' => 'Убыток',
            'ord' => 6,
            'alias' => NULL],
    ];

    /**
     * Добавляет информацию об категориях продавцов
     *
     */
    public function run()
    {
        $updateData = [];

        $tradersParent = $this->query('SELECT id, name FROM ' . $this->table)->fetchAll();

        foreach ($tradersParent as $traderParent) {
            if (isset($this->data[$traderParent['id']])) {
                if ($this->data[$traderParent['id']]['name'] != $traderParent['name']) {
                    $updateData[$traderParent['id']] = $this->data[$traderParent['id']]['name'];
                }
                unset($this->data[$traderParent['id']]);
            }
        }

        //Проверяем стоит ли что-то добавлять
        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
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
