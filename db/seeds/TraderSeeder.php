<?php

use Phinx\Seed\AbstractSeed;

class TraderSeeder extends AbstractSeed
{
    const COUNT_ROWS = 100;

    protected $table = 'trader';
    protected $tableTraderParent = 'trader_parent';

    protected $startName = 'Покупатель №';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    /**
     * Добавляет тестовые данные для покупателей
     *
     */
    public function run()
    {
        $traders = $this->query($this->getSqlCount())->fetchAll();
        if ($traders[0][0] >= self::COUNT_ROWS) {
            return;
        }

        $tradersParent = $this->query($this->getSqlTraderParent())->fetchAll();
        if (empty($tradersParent)) {
            return;
        }

        $this->createData($tradersParent, $traders[0][0]);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert($this->data)->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param array $tradersParent Данные о категориях
     * @param int $tradersCount Количество строк в базе
     */
    protected function createData($tradersParent, $tradersCount)
    {
        $data = [];
        $traderParentId = [];
        foreach ($tradersParent as $traderParent) {
            $traderParentId[$traderParent['id']] = true;
        }

        $inns = $this->getInns();
        $def = (int)$tradersCount == 0;
        for ($i = 1; $i <= self::COUNT_ROWS; $i++) {
            while (true) {
                $inn = rand(301000000000, 301999999999);
                if (!isset($inns[$inn])) {
                    $inns[$inn] = true;
                    break;
                }
            }
            $data[] = [
                'name' => $this->startName . $i,
                'def' => $def,
                'inn' => $inn,
                'parent_id' => (int)array_rand($traderParentId),
            ];
            $def = 0;
        }
        $this->data = $data;
    }

    /**
     * Возвращает строку запроса для получения количества покупателей в системе
     *
     * @return string
     */
    protected function getSqlCount()
    {
        return "SELECT count(id) FROM  " . $this->table;
    }

    /**
     * Возвращает строку запроса для получения категорий покупателей
     *
     * @return string
     */
    protected function getSqlTraderParent()
    {
        return "SELECT id FROM  " . $this->tableTraderParent;
    }

    /**
     * Возвращает список инн из базы, для получения уникальных значений
     *
     * @return array
     */
    protected function getInns()
    {
        $inns = [];

        $innsBd = $this->query("SELECT inn FROM  " . $this->table)->fetchAll();
        if (empty($innsBd)) {
            return $inns;
        }

        foreach ($innsBd as $inn) {
            $inns[$inn['inn']] = true;
        }

        return $inns;
    }
}
