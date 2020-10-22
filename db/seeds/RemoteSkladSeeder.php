<?php

use Phinx\Seed\AbstractSeed;

class RemoteSkladSeeder extends AbstractSeed
{

    const COUNT_ROWS = 300; //количество добавляемых строк
    const PATH_PHOTO = 'foto';

    const TYPE_BLACK = 'black';

    const MIN_WEIGHT_TARA = 800;
    const MAX_WEIGHT_TARA = 15000;

    const MIN_WEIGHT = 40;
    const MAX_WEIGHT = 5000;

    const MIN_SOR = 3;
    const MAX_SOR = 10;

    const MIN_DATE = '01-09-2018'; //Минимальная дата для данных

    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'remote_sklad';
    protected $tableSklad = 'department';
    protected $tableMetal = 'metal';
    protected $startName = 'Поставщик №';

    /**
     * @var array Заполняемые данные
     */
    protected $data = [];

    /**
     * @var array Транспорт
     */
    protected $transport = [
        'Газель', 'Камаз', 'тарантас', 'Ваз', 'джип',
    ];

    /**
     * @var string Строка для генирации номера автомобиля
     */
    protected $numberString = "а.б.в.г.д.е.ж.з.и.к.л.м.н.о.п.р.с.т.я";

    /**
     * @var array Содержит информацию по накладным
     */
    protected $numberAndDateNakl = [];

    /**
     * Добавляет тестовые данные
     *
     */
    public function run()
    {
        $tableInfo = $this->getTableInform();

        if ($tableInfo['count'] >= self::COUNT_ROWS) {
            return;
        }

        $this->createData($tableInfo);

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))->save();
        }
    }

    /**
     * Генирирует информацию для посева
     *
     * @param array $tableInfo
     */
    protected function createData($tableInfo)
    {
        $data = [];

        $sklads = $this->getNameInTable($this->tableSklad, ['type', self::TYPE_BLACK]);
        if (empty($sklads)) {
            return;
        }

        $metal = $this->getNameInTable($this->tableMetal);
        if (empty($metal)) {
            return;
        }

        $startId = $tableInfo['max'] + 1;
        $this->transport = array_flip($this->transport);

        for ($i = $startId; $i <= self::COUNT_ROWS; $i++) {
            $sklad = array_rand($sklads);
            $numberAndDateNakl = $this->getNumberAndDateNakl($sklad);
            $transNumb = $this->getTransportNumber();

            $massa = rand(self::MIN_WEIGHT, self::MAX_WEIGHT);
            $sor = rand(self::MIN_SOR, self::MAX_SOR);
            $primesi = round($massa / 100 * $sor);
            $brute = round($massa + $primesi + $this->getTara($massa));

            $data[] = [
                'id' => $i,
                'report_id' => $i,
                'transport' => array_rand($this->transport),
                'transnumb' => $transNumb,
                'naklnumb' => $numberAndDateNakl['number'],
                'date' => $numberAndDateNakl['date'],
                'massa' => $massa,
                'gruz' => array_rand($metal),
                'sklad' => $sklad,
                'sor' => $sor,
                'brute' => $brute,
                'primesi' => $primesi,
                'time' => $numberAndDateNakl['time'],
                'cena' => null,
                'img' => null,
                'img2' => null,
                'massaFact' => null,
                'destination' => '',
                'comment' => '',
                'finished' => null,
                'transfer' => null,
                'recplate' => $transNumb,
                'path' => self::PATH_PHOTO,
            ];
        }
        $this->data = $data;
    }

    /**
     * Возвращает массив с информацией о количестве строк и последнем добавленном id
     *
     * @return array
     */
    protected function getTableInform()
    {
        $sql = "SELECT MAX(id) as max, COUNT(id) as count FROM " . $this->table;
        return $this->query($sql)->fetchAll()[0];
    }

    /**
     * Возвращает номер автомобиля
     *
     * @return string
     */
    protected function getTransportNumber()
    {
        return $this->getCharsNumber(1) . rand(100, 999) . $this->getCharsNumber(2);
    }

    /**
     * Генирирует случайные буквы для номера
     *
     * @param int $count Количество необходимых букв
     * @return string
     */
    protected function getCharsNumber($count)
    {
        if (is_string($this->numberString)) {
            $this->numberString = array_flip(explode('.', $this->numberString));
        }

        if ($count == 1) {
            return (string)array_rand($this->numberString, $count);
        }

        return implode('', array_rand($this->numberString, $count));
    }

    /**
     * Генирирует номер накладной, время и дату
     *
     * @param string $sklad Название склада
     * @return array
     */
    protected function getNumberAndDateNakl($sklad)
    {
        $date = date('Y-m-d', rand(strtotime(self::MIN_DATE), time()));
        $number = isset($this->numberAndDateNakl[$sklad . $date]) ? $this->numberAndDateNakl[$sklad . $date] : 0;
        $this->numberAndDateNakl[$sklad . $date] = ++$number;

        $timeMin = $number <= 9 ? '0' . $number : $number;
        $time = '09:' . $timeMin;

        return ['date' => $date, 'number' => $number, 'time' => $time];
    }

    /**
     * Возвращает имена из указанной таблицы
     *
     * @param string Название таблицы
     * @param array|string $where
     * @return array
     */
    protected function getNameInTable($table, $where = '')
    {
        $result = [];

        if (!empty($where)) {
            $where = " WHERE {$where[0]} = '{$where[1]}' ";
        }

        $sql = 'SELECT name FROM ' . $table . $where;

        $response = $this->query($sql)->fetchAll();
        if (empty($response)) {
            return $result;
        }
        foreach ($response as $value) {
            $result[$value['name']] = true;
        }

        return $result;
    }

    /**
     * Генирирует массу тары
     *
     * @param int $massa
     * @return int
     */
    protected function getTara($massa)
    {
        $tara = (int)$massa + $massa * (rand(-2, 1) * rand(0, 100) * 0.1) * -1;

        if ($tara < self::MIN_WEIGHT_TARA) {
            $tara = $massa + self::MIN_WEIGHT_TARA;
        }
        if ($tara > self::MAX_WEIGHT_TARA) {
            $tara = $massa + self::MAX_WEIGHT_TARA - self::MAX_WEIGHT;
        }

        return (int)$tara;

    }
}
