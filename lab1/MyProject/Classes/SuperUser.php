<?php
declare(strict_types=1);

namespace MyProject\Classes;

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/SuperUserInterface.php';

/**
 * Класс SuperUser
 * Наследуется от User и реализует SuperUserInterface
 */
class SuperUser extends User implements SuperUserInterface
{
    public string $role;

    /** @var int Счетчик созданных экземпляров класса SuperUser */
    public static int $count = 0;

    /**
     * Конструктор класса SuperUser
     * @param string $name Имя пользователя
     * @param string $login Логин пользователя
     * @param string $password Пароль пользователя
     * @param string $role Роль супер-пользователя
     */
    public function __construct(string $name, string $login, string $password, string $role)
    {
        parent::__construct($name, $login, $password);
        $this->role = $role;
        self::$count++;
    }

    /**
     * Перегруженный метод вывода информации (добавляет вывод роли)
     * @return void
     */
    public function showInfo(): void
    {
        parent::showInfo();
        echo "Роль: {$this->role}\n";
    }

    /**
     * Возвращает ассоциативный массив со свойствами объекта
     * @return array<string, string>
     */
    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'login' => $this->login,
            'password' => $this->getPassword(), // Используем геттер из-за private модификатора в родителе
            'role' => $this->role
        ];
    }
}
