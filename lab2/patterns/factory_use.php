<?php
declare(strict_types=1);

// Явно подключаем все необходимые файлы
require_once __DIR__ . '/factory/router.php';
require_once __DIR__ . '/factory/models/collection.php';
require_once __DIR__ . '/factory/models/user.php';
require_once __DIR__ . '/factory/models/users.php';

use Factory\Router;

$obj = Router::parse('users');
echo $obj->render();


// spl_autoload_register();

// use Factory\Router;

// $obj = Router::parse('users');
// echo $obj->render(); -->

