<?php
declare(strict_types=1);

namespace MyProject\Classes;

/**
 * Класс User
 * Представляет обычного пользователя системы
 */
class User extends AbstractUser
{
    public string $name;
    public string $login;
    protected string $password; 

    /** @var int Счетчик созданных экземпляров класса User */
    public static int $count = 0;

    /**
     * Конструктор класса User
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
        echo "Пользователь {$this->login} удален.\n";
    }

    /**
     * Выводит информацию о пользователе
     */
    public function showInfo(): void
    {
        echo "Имя: {$this->name}, Логин: {$this->login}\n";
    }

    /**
     * Геттер для свойства password
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
