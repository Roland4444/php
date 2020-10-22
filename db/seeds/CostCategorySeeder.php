<?php

use Phinx\Seed\AbstractSeed;

class CostCategorySeeder extends AbstractSeed
{
    const COUNT_ROWS = 250; //количество добавляемых строк

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'cost_category';

    protected $tableGroup = 'cost_category_group';
    protected $startName = 'Категория №';

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
        $costCategories = $this->query($this->getQueryString())->fetchAll();

        if ($costCategories[0][0] >= self::COUNT_ROWS) {
            return;
        }

        $groupIds = $this->getGroupIds();
        if (empty($groupIds)){
            return;
        }

        $this->createData($costCategories[0][0], $groupIds);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param int $costCategoriesCount
     * @param array $groupIds
     */
    protected function createData($costCategoriesCount, $groupIds)
    {
        $data = [];
        $def = (int)$costCategoriesCount == 0;

        for ($i = 1; $i <= self::COUNT_ROWS; $i++) {
            $data[] = [
                'name' => $this->startName . $i,
                'def' => $def,
                'group_id' => array_rand($groupIds),
            ];
            $def = 0;
        }
        $this->data = $data;
    }

    /**
     * Возвращает строку запроса для получения количества поставщиков
     *
     * @return string
     */
    protected function getQueryString()
    {
        return "SELECT count(id) FROM  " . $this->table;
    }

    /**
     * Возвращает существующие в базе группы
     *
     * @return array
     */
    protected function getGroupIds()
    {
        $groupIds = [];
        $GroupIdsBd = $this->query("SELECT id FROM  " . $this->tableGroup)->fetchAll();
        if (empty($GroupIdsBd)){
            return $groupIds;
        }

        foreach ($GroupIdsBd as $GroupIdBd){
            $groupIds[$GroupIdBd['id']] = true;
        }

        return $groupIds;

    }
}
