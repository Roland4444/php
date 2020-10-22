<?php

use Phinx\Seed\AbstractSeed;
use \Reference\Service\PasswordService;

class UserSeeder extends AbstractSeed
{
    /**
     * @var string Таблица для добавления данных
     */
    protected $table = 'user';

    protected $userPassword = 'Qwerty123';

    /**
     * @var string Хеш пароля по умолчанию
     */
    protected $password = '';

    /**
     * @var string Соль пароля по умолчанию
     */
    protected $pass = '';

    /**
     * @var array Данные пользователей
     */
    protected $data = [];

    public function run()
    {
        $this->setPasswordData();
        $this->setData();
        $users = $this->query($this->getQueryString())->fetchAll();

        foreach ($users as $user) {
            if (isset($this->data[$user['login']])) {
                unset($this->data[$user['login']]);
            }
        }

        if (!empty($this->data)) {
            $table = $this->table($this->table);
            $table->insert(array_values($this->data))
                ->save();
        }
    }

    /**
     * Создает строку запроса для поиска пользователей из базы
     *
     * @return string
     */
    private function getQueryString()
    {
        return "SELECT login FROM " . $this->table . " WHERE login IN ('" . implode("', '", array_keys($this->data)) . "')";
    }

    /**
     * Заполняет пароль и соль из пароля пользователя
     */
    private function setPasswordData()
    {
        $passwordData = PasswordService::getPasswordData($this->userPassword);
        $this->password = $passwordData['password'];
        $this->pass = $passwordData['pass'];
    }

    /**
     * Заполняеет данные для посева
     *
     */
    protected function setData()
    {
        $this->data = [
            'admin' => [
                'name' => 'admin',
                'login' => 'admin',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '1'
            ],
            'sklad' => [
                'name' => 'sklad',
                'login' => 'sklad',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '2'
            ],
            'minisklad' => [
                'name' => 'minisklad',
                'login' => 'minisklad',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '3'
            ],
            'viewer' => [
                'name' => 'viewer',
                'login' => 'viewer',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '4'
            ],
            'vehicle' => [
                'name' => 'vehicle',
                'login' => 'vehicle',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '9'
            ],
            'colorSklad' => [
                'name' => 'colorSklad',
                'login' => 'colorSklad',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '10'
            ],
            'colorMiniSklad' => [
                'name' => 'colorMiniSklad',
                'login' => 'colorMiniSklad',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '11'
            ],
            'miniSkladViewer' => [
                'name' => 'miniSkladViewer',
                'login' => 'miniSkladViewer',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '12'
            ],
            'security' => [
                'name' => 'security',
                'login' => 'security',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '13'
            ],
            'glavbuh' => [
                'name' => 'glavbuh',
                'login' => 'glavbuh',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '14'
            ],
            'officecash' => [
                'name' => 'officecash',
                'login' => 'officecash',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '15'
            ],
            'NonFerrousShipment' => [
                'name' => 'NonFerrousShipment',
                'login' => 'NonFerrousShipment',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '16'
            ],
            'NonFerrousCash' => [
                'name' => 'NonFerrousCash',
                'login' => 'NonFerrousCash',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '17'
            ],
            'NonFerrousStorage' => [
                'name' => 'NonFerrousStorage',
                'login' => 'NonFerrousStorage',
                'password' => $this->password,
                'pass' => $this->pass,
                'role_id' => '18'
            ],
        ];
    }
}
