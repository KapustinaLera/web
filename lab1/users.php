<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    // Префикс нашего пространства имён
    $prefix = 'MyProject\\Classes\\';
    
    // Базовая директория, где лежат файлы классов
    $baseDir = __DIR__ . '/MyProject/Classes/';

    // Проверяем, принадлежит ли класс нашему пространству имён
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Если нет — выходим, пусть другой загрузчик попробует найти класс
        return;
    }

    // Получаем относительное имя класса без префикса
    $relativeClass = substr($class, $len);

    // Заменяем разделители неймспейсов (\) на разделители путей (/)
    // и добавляем расширение .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Если файл существует — подключаем его
    if (file_exists($file)) {
        require $file;
    }
});

use MyProject\Classes\User;
use MyProject\Classes\SuperUser;

echo "<pre>";

echo "Создание обычных пользователей\n";
$user1 = new User('Петр Иванов', 'ivanov', 'pass123');
$user2 = new User('Иван Петров', 'petrov', 'pass456');
$user3 = new User('Ксения Сидорова', 'sidorova', 'pass789');

echo "\nСоздание супер-пользователя \n";
$superUser = new SuperUser('Админ Админов', 'admin', 'adminpass', 'Administrator');

echo "\nИнформация о пользователях\n";
$user1->showInfo();
$user2->showInfo();
$user3->showInfo();
$superUser->showInfo();

echo "\nМетод getInfo() для супер-пользователя \n";
print_r($superUser->getInfo());

echo "\nСтатистика\n";
echo "Всего обычных пользователей: " . User::$count . "\n";
echo "Всего супер-пользователей: " . SuperUser::$count . "\n";

echo "\nЗавершение скрипта (автоматический вызов деструкторов)\n";

// Явное уничтожение объектов до закрытия </pre>, 
// чтобы деструкторы вывелись внутри блока preformatted text
unset($user1, $user2, $user3, $superUser);

echo "</pre>";
