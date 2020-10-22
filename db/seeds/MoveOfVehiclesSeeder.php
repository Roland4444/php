<?php

use Phinx\Seed\AbstractSeed;

class MoveOfVehiclesSeeder extends AbstractSeed
{
    const COUNT_ROWS = 300; //количество добавляемых строк
    const ID_BABAEVSKY = 2; //id отделения для добавления данных

    const START_NAME_CUSTOMER = 'Покупатель №';

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'move_of_vehicles';
    protected $tableDepartment = 'department';
    protected $tableVehicle = 'vehicle';
    protected $tableRemoteSklad = 'remote_sklad';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    /**
     * @var string Название департамента для выборки
     */
    protected $nameBabaevsky = '';

    /**
     * @var array Адреса
     */
    protected $addresses = [
        'г. Астрахань ул. Никольская',
        'г. Астрахань ул. Добрынинская',
        'г. Астрахань ул. Мытищь',
        'г. Саратов ул. Первомайская',
    ];

    /**
     * Добавляет тестовые данные
     *
     */
    public function run()
    {
        $this->setNameDepartment();
        if (empty($this->nameBabaevsky)) {
            return;
        }

        $this->createData();

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     */
    protected function createData()
    {
        $data = [];

        $remoteSkladInfo = $this->getRemoteSkladInfo();
        if (empty($remoteSkladInfo)) {
            return;
        }

        $vehicleIds = $this->getVehicleIds();
        if (empty($vehicleIds)) {
            return;
        }

        $alreadyInsertData = $this->getAlreadyInsertData();

        $number = 0;
        $addresses = array_flip($this->addresses);
        foreach ($remoteSkladInfo as $value) {
            if (isset($alreadyInsertData[$value['date'] . $value['naklnumb']])) {
                continue;
            }

            $data[] = [
                'date' => $value['date'],
                'customer' => self::START_NAME_CUSTOMER . ++$number,
                'vehicle_id' => array_rand($vehicleIds),
                'waybill' => $value['naklnumb'],
                'payment' => rand(0, 7000),
                'department_id' => self::ID_BABAEVSKY,
                'comment' => array_rand($addresses),
                'completed' => rand(0, 1),
                'money_department_id' => self::ID_BABAEVSKY
            ];
        }
        $this->data = $data;
    }

    /**
     * Устанавливает имя департамента из таблицы с id = self::ID_BABAEVSKY
     *
     */
    protected function setNameDepartment()
    {
        $sql = "SELECT name FROM " . $this->tableDepartment . " WHERE id = " . self::ID_BABAEVSKY;
        $this->nameBabaevsky = $this->query($sql)->fetchAll()[0]['name'];
    }

    /**
     * Возвращает информацию об приеме на складе с id = self::ID_BABAEVSKY
     *
     * @return array
     */
    protected function getRemoteSkladInfo()
    {
        $sql = "SELECT date, sklad, naklnumb FROM " . $this->tableRemoteSklad .
            " WHERE sklad = '" . $this->nameBabaevsky . "' " .
            " ORDER BY date ";

        return $this->query($sql)->fetchAll();
    }


    /**
     * Возвращает id транспортных средста
     *
     * @return array
     */
    protected function getVehicleIds()
    {
        $result = [];
        $sql = "SELECT id FROM " . $this->tableVehicle;
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
     * Возвращает данные информацию об существующих записях в таблице
     *
     * @return array
     */
    protected function getAlreadyInsertData()
    {
        $result = [];
        $sql = "SELECT  date,  waybill FROM " . $this->table;
        $response = $this->query($sql)->fetchAll();

        if (empty($response)) {
            return $result;
        }

        foreach ($response as $value) {
            $key = $value['date'] . $value['waybill'];
            $result[$key] = true;
        }
        return $result;
    }
}
