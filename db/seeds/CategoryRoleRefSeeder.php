<?php

use Phinx\Seed\AbstractSeed;

class CategoryRoleRefSeeder extends AbstractSeed
{
    const COUNT_ROWS = 1000;
    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'category_role_ref';

    protected $tableRole = 'role';
    protected $tableCostCategory = 'cost_category';

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
        $countCategoryRoleRef = $this->getCountRows($this->table);

        if ($countCategoryRoleRef >= self::COUNT_ROWS) {
            return;
        }

        $categoryIds = $this->getIds($this->tableCostCategory);
        if (empty($categoryIds)) {
            return;
        }

        $roleIds = $this->getIds($this->tableRole);
        if (empty($roleIds)) {
            return;
        }

        $this->createData($countCategoryRoleRef, $categoryIds, $roleIds);


        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Создает данные для заполнения
     *
     * @param int $countCategoryRoleRef Количество строк в заполняемой таблице
     * @param array $categoryIds
     * @param array $roleIds
     */
    protected function createData($countCategoryRoleRef, $categoryIds, $roleIds){
        $date = [];
        foreach ($categoryIds as $idCategory => $item){
            $countJoinRole = rand(1,4);
            for ($i = 1; $i <= $countJoinRole; $i++ ){
                $categoryId = array_rand($categoryIds);
                $roleId = array_rand($roleIds);
                $date[$categoryId . $roleId] = [
                    'category_id' => $categoryId,
                    'role_id' => $roleId,
                ];
                if(++$countCategoryRoleRef >= self::COUNT_ROWS){
                    break 2;
                }
            }
        }

        $this->data = $date;
    }

    /**
     * Возвраращает количество строк в указанной таблице
     *
     * @param $table
     * @return int
     */
    protected function getCountRows($table){

        $result = $this->query("SELECT count(id) FROM  " . $table)->fetchAll();
        return $result[0][0];
    }

    /**
     * Возвращает id указанной таблицы
     *
     * @param string $table
     * @return array
     */
    protected function getIds($table)
    {
        $ids = [];
        $idsBd = $this->query("SELECT id FROM  " . $table)->fetchAll();
        if (empty($idsBd)) {
            return $ids;
        }

        foreach ($idsBd as $id) {
            $ids[$id['id']] = true;
        }
        return $ids;
    }


}
