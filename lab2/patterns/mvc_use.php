<?php
declare(strict_types=1);

// Подключаем все необходимые файлы
require_once __DIR__ . '/mvc/controllers/router.php';
require_once __DIR__ . '/mvc/controllers/controller.php';
require_once __DIR__ . '/mvc/decorators/decoratorfactory.php';
require_once __DIR__ . '/mvc/decorators/userdecorator.php';   // ← ДОБАВИЛИ ЭТУ СТРОКУ
require_once __DIR__ . '/mvc/decorators/usersdecorator.php';
require_once __DIR__ . '/mvc/models/user.php';
require_once __DIR__ . '/mvc/models/users.php';
require_once __DIR__ . '/mvc/views/viewfactory.php';
require_once __DIR__ . '/mvc/views/htmlview.php';
require_once __DIR__ . '/mvc/views/markdownview.php';
require_once __DIR__ . '/mvc/views/usersmdview.php';

use MVC\Controllers\Controller;

// Создаём контроллер для вывода списка пользователей в Markdown
$obj = new Controller('users.md');
echo "<pre>";
echo $obj->render();
echo "</pre>";


// spl_autoload_register();

// use MVC\Controllers\Controller;

// //$obj = new Controller('users.rss');
// $obj = new Controller('users/1.html');
// echo $obj->render();
