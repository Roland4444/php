<?php

namespace Reference\Service;

class PasswordService
{
    /**
     * Возвращает секретную фразу
     *
     * @return string
     */
    private static function getSecretPhrase()
    {
        return 'S2dV5mYTzPBz7rDANbGey35ZL7W2PFXB';
    }

    /**
     * Возвращает соль
     *
     * @return string
     */
    private static function getSalt()
    {
        return substr(sha1(self::getSecretPhrase()), 0, 12);
    }

    /**
     * @return string
     */
    private static function getPass()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * @param $pass
     *
     * @return string
     */
    private static function getUniqueSalt($pass)
    {
        return self::getSalt() . substr($pass, 0, 10);
    }

    /**
     * @param $password
     * @param $uniqueSalt
     *
     * @return string
     */
    private static function getPassword($password, $uniqueSalt)
    {
        return sha1(crypt($password, '$2a$10$' . $uniqueSalt));
    }

    /**
     * Добавляет в модель пароль
     *
     * @param string $rowPassword
     * @param null   $salt
     *
     * @return array
     */
    public static function getPasswordData($rowPassword, $salt = null)
    {
        if (! $salt) {
            $salt = self::getPass();
        }
        $password = self::getPassword($rowPassword, self::getUniqueSalt($salt));
        return ['pass' => $salt, 'password' => $password];
    }
}
