<?php
declare(strict_types=1);

namespace MyProject\Classes;

require_once __DIR__ . '/AbstractUser.php';

/**
 * Класс User
 * Представляет обычного пользователя системы
 */
class User extends AbstractUser
{
    public string $name;
    public string $login;
    private string $password;

    /** @var int Счетчик созданных экземпляров класса User */
    public static int $count = 0;

    /**
     * Конструктор класса User
     * @param string $name Имя пользователя
     * @param string $login Логин пользователя
     * @param string $password Пароль пользователя
     */
    public function __construct(string $name, string $login, string $password)
    {
        $this->name = $name;
        $this->login = $login;
        $this->password = $password;
        self::$count++;
    }

    /**
     * Деструктор класса User
     */


    public function __destruct()
    {
    echo "Пользователь {$this->login} удален.<br>\n";
    }

    /**
     * Выводит информацию о пользователе
     * @return void
     */
    public function showInfo(): void
    {
        echo "Имя: {$this->name}, Логин: {$this->login}\n";
    }

    /**
     * Геттер для приватного свойства password.
     * Необходим для доступа к значению из класса-наследника (SuperUser),
     * так как прямое обращение к private свойству из наследника запрещено в PHP.
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
