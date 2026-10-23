<?php
declare(strict_types=1);

// Регистрация функции автозагрузки классов
spl_autoload_register(function (string $class): void {
    $prefix = 'MyProject\\Classes\\';
    $base_dir = __DIR__ . '/MyProject/Classes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use MyProject\Classes\User;
use MyProject\Classes\SuperUser;

echo "Создание обычных пользователей\n";
$user1 = new User('Петр Иванов', 'ivanov', 'pass123');
$user2 = new User('Иван Петров', 'petrov', 'pass456');
$user3 = new User('Ксения Сидорова', 'sidorova', 'pass789');

echo "\nСоздание супер-пользователя\n";
$superUser = new SuperUser('Админ Админов', 'admin', 'adminpass', 'Administrator');

echo "\nИнформация о пользователях\n";
$user1->showInfo();
$user2->showInfo();
$user3->showInfo();
$superUser->showInfo();

echo "\nМетод getInfo() для супер-пользователя\n";
print_r($superUser->getInfo());

echo "\nСтатистика\n";
echo "Всего обычных пользователей: " . User::$count . "\n";
echo "Всего супер-пользователей: " . SuperUser::$count . "\n";

echo "\nЗавершение скрипта (автоматический вызов деструкторов)\n";
// Деструкторы будут вызваны автоматически при завершении работы скрипта
