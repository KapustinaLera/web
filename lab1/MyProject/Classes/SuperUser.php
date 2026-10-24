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
    
    protected string $password;

    /** @var int Счетчик созданных экземпляров класса SuperUser */
    public static int $count = 0;

    /**
     * Конструктор класса SuperUser
     * Инициализирует свойства напрямую без вызова parent::__construct()
     */
    public function __construct(string $name, string $login, string $password, string $role)
    {
        $this->name = $name;
        $this->login = $login;
        $this->password = $password; 
        $this->role = $role;
        
        self::$count++;
    }

    /**
     * Перегруженный метод вывода информации
     */
    public function showInfo(): void
    {
        echo "Имя: {$this->name}, Логин: {$this->login}\n";
        echo "Роль: {$this->role}\n";
    }

    /**
     * Возвращает ассоциативный массив со свойствами объекта
     */
    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'login' => $this->login,
            'password' => $this->password, // Прямой доступ к protected свойству
            'role' => $this->role
        ];
    }
}
